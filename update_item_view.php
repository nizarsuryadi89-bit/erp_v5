<?php
$file = '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/views/admin/wip/components/v_table_item_view_core.php';
$content = file_get_contents($file);

$tanggal_end = <<<'HTML'
                        </div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="d-flex flex-column gap-1">
                            <div class="fw-bold text-dark text-uppercase"
HTML;

$tanggal_new = <<<'HTML'
                        </div>
                    </td>
                    <!-- Deadline -->
                    <td style="vertical-align: middle;">
                        <?php if (!empty($item->dateline_tgl) && $item->dateline_tgl != '0000-00-00'): ?>
                            <div class="fw-bold text-danger"><i class="fas fa-hourglass-half me-1"></i> <?= date('d/m/Y', strtotime($item->dateline_tgl)) ?></div>
                            <div class="text-danger small"><?= !empty($item->dateline_jam) ? date('H:i', strtotime($item->dateline_jam)) : '-' ?></div>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="d-flex flex-column gap-1">
                            <div class="fw-bold text-dark text-uppercase"
HTML;
$content = str_replace($tanggal_end, $tanggal_new, $content);

$konsumen_end = <<<'HTML'
                            <?php endif; ?>


                        </div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="d-flex flex-column gap-1">
                            <?php if ($is_urgent_item): ?>
HTML;

$konsumen_new = <<<'HTML'
                            <?php endif; ?>


                        </div>
                    </td>
                    <!-- Deskprint -->
                    <td style="vertical-align: middle;">
                        <?php if (!empty($item->deskprint_nama)): ?>
                            <span class="fw-bold text-dark"><i class="fas fa-user-edit me-1 text-secondary"></i> <?= htmlspecialchars($item->deskprint_nama) ?></span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="d-flex flex-column gap-1">
                            <?php if ($is_urgent_item): ?>
HTML;
$content = str_replace($konsumen_end, $konsumen_new, $content);

$produk_end = <<<'HTML'
                            <?php if (!empty($item->bom_lines)): ?>
                                <?php
                                $bom_json_data = [];
                                foreach ($item->bom_lines as $bl) {
                                    $total_qty = $bl->bom_qty * $qty;
                                    $bom_json_data[] = [
                                        'name' => $bl->bahan_nama ?? 'Unknown',
                                        'qty' => number_format($total_qty, 2),
                                        'unit' => $bl->satuan_nama ?? 'Unit'
                                    ];
                                }
                                ?>
                                <div class="mt-2">
                                    <button type="button"
                                        class="btn btn-sm btn-light border border-secondary border-opacity-25 shadow-sm text-dark fw-bold w-100 text-start btn-show-bom-modal"
                                        style="font-size: 0.65rem;" data-product="<?= htmlspecialchars($item->product_nama) ?>"
                                        data-bom="<?= htmlspecialchars(json_encode($bom_json_data), ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fas fa-box-open text-primary me-1"></i> LIHAT BAHAN BAKU (BOM)
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
HTML;

$produk_new = <<<'HTML'
                            <?php if (!empty($item->bom_lines)): ?>
                                <?php
                                $bom_json_data = [];
                                foreach ($item->bom_lines as $bl) {
                                    $total_qty = $bl->bom_qty * $qty;
                                    $bom_json_data[] = [
                                        'name' => $bl->bahan_nama ?? 'Unknown',
                                        'qty' => number_format($total_qty, 2),
                                        'unit' => $bl->satuan_nama ?? 'Unit'
                                    ];
                                }
                                ?>
                                <div class="mt-2">
                                    <button type="button"
                                        class="btn btn-sm btn-light border border-secondary border-opacity-25 shadow-sm text-dark fw-bold w-100 text-start btn-show-bom-modal"
                                        style="font-size: 0.65rem;" data-product="<?= htmlspecialchars($item->product_nama) ?>"
                                        data-bom="<?= htmlspecialchars(json_encode($bom_json_data), ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fas fa-box-open text-primary me-1"></i> LIHAT BAHAN BAKU (BOM)
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Merged Ukuran & QTY -->
                            <div class="mt-2 p-2 bg-light border rounded shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <span class="badge bg-secondary rounded-pill">QTY: <?= $qty ?> PCS</span>
                                <?php if ($item->kategori_id == 5): ?>
                                    <span class="text-dark fw-bold" style="font-size:0.75rem;">Dimensi: <?= $p ?> M x <?= htmlspecialchars($l > 0 ? $l : ($meta['ket_1'] ?? $item->db_ket_1 ?? (isset($curMachine['raw_width']) ? $curMachine['raw_width'] : '?'))) ?></span>
                                <?php elseif ($item->kategori_id == 9): ?>
                                    <span class="text-dark fw-bold" style="font-size:0.75rem;">Panjang: <?= $p ?> ML</span>
                                <?php elseif ($item->panjang > 0 && $item->lebar > 0): ?>
                                    <span class="text-dark fw-bold" style="font-size:0.75rem;">Dimensi: <?= $p ?> x <?= $l ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic" style="font-size:0.75rem;">Sesuai BOM</span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </td>
HTML;
$content = str_replace($produk_end, $produk_new, $content);

$metode_end = <<<'HTML'
                        <?php else: ?>
                            <span class="badge bg-light text-dark border border-dark border-opacity-25 shadow-sm fw-900 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-store me-1"></i> OFFLINE
                            </span>
                        <?php endif; ?>
                    </td>
HTML;

$metode_new = <<<'HTML'
                        <?php else: ?>
                            <span class="badge bg-light text-dark border border-dark border-opacity-25 shadow-sm fw-900 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-store me-1"></i> OFFLINE
                            </span>
                        <?php endif; ?>
                    </td>
                    <!-- Lokasi Pesanan -->
                    <td style="vertical-align: middle;">
                        <?php
                        $lokasi_cetak = $item->lokasi_cetak ?? 0;
                        $lokasi_nama = 'CABANG LAIN';
                        $branchColor = '#607d8b';
                        if ($lokasi_cetak == 2) {
                            $lokasi_nama = 'TGM OFFICIAL (PUSAT)';
                            $branchColor = '#2196f3';
                        } elseif ($lokasi_cetak == 3) {
                            $lokasi_nama = 'TGM MERCH (CABANG)';
                            $branchColor = '#ff9800';
                        } else {
                            $lokasi_nama = 'OFFLINE/CABANG';
                        }
                        ?>
                        <div class="company-pill"
                            style="color: <?= $branchColor ?>; border: 1px solid <?= $branchColor ?>20;">
                            <div class="company-dot" style="background: <?= $branchColor ?>"></div>
                            <?= $lokasi_nama ?>
                        </div>
                        <?php if ($item->perusahaan_id != $this->session->userdata('perusahaan_id')): ?>
                            <span
                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill pulse-red"
                                style="padding: 3px 6px; font-size: 0.55rem; display: block; width: fit-content; margin-top: 5px;">
                                <i class="fas fa-exchange-alt me-1"></i>TITIPAN DARI
                                <?= strtoupper($item->nama_perusahaan ?? 'CABANG LAIN') ?>
                            </span>
                        <?php endif; ?>
                    </td>
HTML;
$content = str_replace($metode_end, $metode_new, $content);


// Remove the Ukuran & QTY column block manually
// It starts with <td class="text-center" style="vertical-align: middle;"> after Metode
// And ends right before the status column: <td class="text-center" style="vertical-align: middle;"> \n <div class="d-flex flex-column align-items-center gap-1"> \n <span class="badge rounded-pill border <?= $stClass
$start_marker = '<td class="text-center" style="vertical-align: middle;">
                        <?php
                        // Cek apakah produk ini memiliki BOM 1D (Meter Lari/Pcs) tanpa PxL';

$end_marker = '<td class="text-center" style="vertical-align: middle;">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <span class="badge rounded-pill border <?= $stClass ?>"';

$pos_start = strpos($content, $start_marker);
$pos_end = strpos($content, $end_marker, $pos_start);
if ($pos_start !== false && $pos_end !== false) {
    $content = substr($content, 0, $pos_start) . $end_marker . substr($content, $pos_end + strlen($end_marker));
} else {
    echo "Could not find block to remove!\n";
}

file_put_contents($file, $content);
echo "Success";
