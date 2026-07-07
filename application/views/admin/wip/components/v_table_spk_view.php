<div class="table-responsive">
    <table id="tableSemuaProduksi" class="table premium-table table-hover align-middle mb-0 w-100" style="white-space: nowrap;">
        <thead class="bg-light text-muted">
            <tr>
                <th class="text-center" width="50">No.</th>
                <th>Spk</th>
                <th>Tanggal Pesanan</th>
                <th>Deadline</th>
                <th>Konsumen</th>
                <th>Deskprint</th>
                <th>Produk</th>
                <th>Metode Pesanan</th>
                <th>Lokasi Pesanan</th>
                <th class="text-center">Status Pesanan</th>
                <th class="text-center">Tindakan</th>
                <th class="d-none">StatusTags</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $branchColors = ['#2196f3', '#9c27b0', '#4caf50', '#ff9800', '#e91e63', '#8bc34a', '#009688', '#ffc107', '#673ab7', '#3f51b5'];

            foreach ($produksi_list as $nospk => $group_items):
                $p = $group_items[0];
                $itemCount = count($group_items);

                $adaPending = $adaProses = $adaDone = $adaOnPB = $isReprintGroup = false;
                $rowCats = [];

                foreach ($group_items as $item) {
                    $st = (string) ($item->status_produksi ?? '0');
                    if (in_array($st, ['approved', 'pending', '', '0']))
                        $adaPending = true;
                    elseif ($st === 'proses')
                        $adaProses = true;
                    elseif ($st === 'done')
                        $adaDone = true;
                    elseif ($st === 'on_pb')
                        $adaOnPB = true;

                    if (!empty($item->is_reprint) || strpos(($item->catatan ?? ''), '[RIJEK]') !== false) {
                        $isReprintGroup = true;
                    }

                    if ($item->kategori_id)
                        $rowCats[] = $item->kategori_id;
                }
                $rowCats = array_values(array_unique($rowCats));

                // Check if ALL currently processing items are in their FINAL step
                $allItemsFinalStep = true;
                foreach ($group_items as $itm) {
                    if ($itm->status_produksi == 'proses') {
                        if ($itm->current_step_urutan < $itm->total_steps) {
                            $allItemsFinalStep = false;
                            break;
                        }
                    }
                }

                // Initial Aggregate Status
                $aggStatus = 'ANTRIAN';
                $aggClass = 'bg-secondary bg-opacity-10 text-secondary border-secondary';
                $badgeIcon = '<i class="fas fa-clock me-1"></i>';

                if ($adaPending) {
                    $aggStatus = 'ANTRIAN';
                    $aggClass = 'bg-warning bg-opacity-10 text-warning border-warning';
                    $badgeIcon = '<i class="fas fa-clock me-1"></i>';
                } elseif ($adaProses) {
                    if ($allItemsFinalStep) {
                        $aggStatus = 'FINISHING';
                        $aggClass = 'bg-info bg-opacity-10 text-info border-info';
                        $badgeIcon = '<i class="fas fa-magic me-1"></i>';
                    } else {
                        $aggStatus = 'PROSES';
                        $aggClass = 'bg-primary bg-opacity-10 text-primary border-primary';
                        $badgeIcon = '<i class="fas fa-cog fa-spin me-1"></i>';
                    }
                } elseif ($adaDone) {
                    $aggStatus = 'SIAP PB';
                    $aggClass = 'bg-success bg-opacity-10 text-success border-success';
                    $badgeIcon = '<i class="fas fa-check-circle me-1"></i>';

                    if ($adaOnPB) {
                        $aggStatus = 'SIAP PB (Parsial)';
                    }
                } elseif ($adaOnPB) {
                    $aggStatus = 'DIKIRIM PB';
                    $aggClass = 'bg-primary bg-opacity-10 text-primary border-primary';
                    $badgeIcon = '<i class="fas fa-paper-plane me-1"></i>';
                }

                $isReadyToPB = (!$adaPending && !$adaProses && $adaDone);
                $readyPbClass = $isReadyToPB ? 'tr-ready-pb bg-success bg-opacity-10' : '';

                $isSentToPB = (!$adaPending && !$adaProses && !$adaDone && $adaOnPB);
                $sentPbClass = $isSentToPB ? 'tr-sent-pb opacity-75' : '';

                $rowClass = $readyPbClass . ' ' . $sentPbClass . ($isReprintGroup ? ' tr-reprint' : '');
                
                $comp_name = $p->nama_perusahaan ?: 'N/A';
                $hash = array_sum(array_map('ord', str_split($comp_name)));
                $branchColor = $branchColors[$hash % count($branchColors)];
                $borderClass = $isReadyToPB ? '#2ecc71' : ($isSentToPB ? '#3498db' : ($isReprintGroup ? '#e74c3c' : $branchColor));
                ?>
                <tr class="tr-spk-row <?= $rowClass ?>" data-categories='<?= json_encode($rowCats) ?>'
                    style="border-left: 5px solid <?= $borderClass ?> !important;">
                    <!-- No -->
                    <td class="text-center text-muted fw-bold">
                        <?= $no++ ?>
                    </td>
                    
                    <!-- Spk -->
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="spk-code">
                                    <?= $p->nospk ?>
                                </span>
                                <?php if ($isReprintGroup): ?>
                                    <span class="reprint-indicator"><i class="fas fa-redo"></i> REPRINT</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-1 mt-1">
                                <button class="btn-action-premium btn-primary-p py-1 px-2" style="font-size: 0.65rem;"
                                    onclick="previewTagSPK('<?= implode(',', array_column($group_items, 'produksi_id')) ?>')" title="Cetak Tag Seluruh SPK"><i
                                        class="fas fa-tags me-1"></i>Cetak Tag</button>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Tanggal Pesanan -->
                    <td>
                        <?php if ($p->tgl_spk): ?>
                            <div class="fw-bold"><i class="far fa-clock me-1 text-primary"></i> <?= date('d/m/Y', strtotime($p->tgl_spk)) ?></div>
                            <div class="text-muted small"><?= date('H:i', strtotime($p->tgl_spk)) ?></div>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <!-- Deadline -->
                    <td>
                        <?php if (!empty($p->dateline_tgl) && $p->dateline_tgl != '0000-00-00'): ?>
                            <div class="fw-bold text-danger"><i class="fas fa-hourglass-half me-1"></i> <?= date('d/m/Y', strtotime($p->dateline_tgl)) ?></div>
                            <div class="text-danger small"><?= !empty($p->dateline_jam) ? date('H:i', strtotime($p->dateline_jam)) : '-' ?></div>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Konsumen -->
                    <td class="fw-800 text-dark">
                        <?= $p->konsumen_nama ?: '-' ?>
                    </td>

                    <!-- Deskprint -->
                    <td>
                        <?php if (!empty($p->deskprint_nama)): ?>
                            <span class="fw-bold text-dark"><i class="fas fa-user-edit me-1 text-secondary"></i> <?= htmlspecialchars($p->deskprint_nama) ?></span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Produk -->
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div class="fw-bold text-dark mb-1">
                                <span class="badge bg-secondary rounded-pill"><?= count($group_items) ?> Item</span>
                            </div>
                            <div class="text-muted small" style="min-width: 200px; max-width: 300px; line-height: 1.3; white-space: normal;">
                                <?php foreach ($group_items as $it): ?>
                                    <div class="mb-1 pb-1">
                                        <i class="fas fa-caret-right text-primary me-1"></i>
                                        <span class="text-dark fw-bold"><?= $it->product_nama ?></span>
                                        <?php if ($it->panjang > 0 && $it->lebar > 0): ?>
                                            <span class="text-muted fst-italic ms-1" style="font-size: 0.65rem;">
                                                (<?= (float) $it->panjang ?> x <?= (float) $it->lebar ?>m)
                                            </span>
                                        <?php endif; ?>
                                        <span class="fw-bold ms-1">x<?= $it->qty ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            $spk_op_cetak = [];
                            $spk_op_finishing = [];
                            foreach ($group_items as $it) {
                                if (!empty($it->op_cetak_name)) $spk_op_cetak[] = $it->op_cetak_name;
                                if (!empty($it->op_finishing_name)) $spk_op_finishing[] = $it->op_finishing_name;
                            }
                            $spk_op_cetak = array_unique(array_filter(array_map('trim', explode(',', implode(',', $spk_op_cetak)))));
                            $spk_op_finishing = array_unique(array_filter(array_map('trim', explode(',', implode(',', $spk_op_finishing)))));
                            ?>
                            
                            <?php 
                                $pb_oleh = null;
                                $pb_via = null;
                                $pb_note = null;
                                $clean_catatan = [];
                                if (!empty($p->catatan)) {
                                    $lines = explode("\n", $p->catatan);
                                    foreach ($lines as $line) {
                                        if (preg_match('/\[(?:BULK )?HANDOVER\].*?\|\s*Oleh:\s*(.*?)\s*\|\s*Via:\s*(.*?)\s*\|\s*Note:\s*(.*)/i', $line, $matches)) {
                                            $pb_oleh = trim($matches[1]);
                                            $pb_via = trim($matches[2]);
                                            $pb_note = trim($matches[3]);
                                            if ($pb_note === '-') $pb_note = null;
                                        } elseif (preg_match('/\[PB\].*?Dikirim ke Hub Pengambilan Barang/i', $line)) {
                                            // Skip noisy line
                                        } else {
                                            $clean_catatan[] = $line;
                                        }
                                    }
                                }
                                $final_catatan = trim(implode("\n", $clean_catatan));
                            ?>
                            <?php if (!empty($final_catatan) || !empty($spk_op_cetak) || !empty($spk_op_finishing) || !empty($pb_oleh)): ?>
                                <div class="mt-1 p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25"
                                    style="max-width:300px; white-space: normal;">
                                    <div class="small fw-bold text-warning-emphasis mb-1"><i
                                            class="fas fa-sticky-note me-1"></i>KETERANGAN:</div>
                                    
                                    <?php if (!empty($spk_op_cetak) || !empty($spk_op_finishing) || !empty($pb_oleh)): ?>
                                        <div class="<?= !empty($final_catatan) ? 'mb-1 pb-1 border-bottom border-warning border-opacity-25' : '' ?>" style="font-size: 0.65rem;">
                                            <?php if (!empty($spk_op_cetak)): ?>
                                                <div class="text-muted"><i class="fas fa-print me-1"></i>Dikerjakan Oleh Operator: <span class="fw-bold text-dark"><?= implode(', ', $spk_op_cetak) ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($spk_op_finishing)): ?>
                                                <div class="text-muted"><i class="fas fa-cut me-1"></i>finishing oleh: <span class="fw-bold text-dark"><?= implode(', ', $spk_op_finishing) ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($pb_oleh)): ?>
                                                <div class="text-muted"><i class="fas fa-handshake me-1"></i>di berikan ke konsumen oleh: <span class="fw-bold text-dark"><?= htmlspecialchars($pb_oleh) ?> (PB)</span> melalui: <span class="fw-bold text-dark"><?= htmlspecialchars($pb_via) ?></span></div>
                                                <?php if (!empty($pb_note)): ?>
                                                    <div class="text-muted ps-3"><i class="fas fa-comment-dots me-1"></i>Note PB: <span class="fst-italic text-dark"><?= htmlspecialchars($pb_note) ?></span></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($final_catatan)): ?>
                                        <div class="text-dark small lh-sm fst-italic"><?= nl2br(htmlspecialchars($final_catatan)) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>

                    <!-- Metode Pesanan -->
                    <td>
                        <?php if (!empty($p->nama_jenis_transaksi)): ?>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 shadow-sm fw-900" style="font-size: 0.65rem;">
                                <i class="fas fa-shopping-cart me-1"></i> <?= strtoupper($p->nama_jenis_transaksi) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Lokasi Pesanan -->
                    <td>
                        <div class="company-pill"
                            style="color: <?= $branchColor ?>; border: 1px solid <?= $branchColor ?>20;">
                            <div class="company-dot" style="background: <?= $branchColor ?>"></div>
                            <?= $comp_name ?>
                        </div>
                        <?php if ($p->perusahaan_id != $this->session->userdata('perusahaan_id')): ?>
                            <span
                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill pulse-red"
                                style="padding: 3px 6px; font-size: 0.55rem; display: block; width: fit-content; margin-top: 5px;">
                                <i class="fas fa-exchange-alt me-1"></i>TITIPAN DARI
                                <?= strtoupper($p->nama_perusahaan ?? 'CABANG LAIN') ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Status Pesanan -->
                    <td>
                        <span class="status-badge-premium border <?= $aggClass ?> w-100 d-block text-center">
                            <?= $badgeIcon . $aggStatus ?>
                        </span>
                        <?php if ($adaProses): ?>
                            <?php
                            $totalSteps = 0;
                            $doneSteps = 0;
                            $activeLocations = [];
                            foreach ($group_items as $it) {
                                $totalSteps += $it->total_steps;
                                $doneSteps += $it->done_steps;
                                foreach (($it->routing_steps ?? []) as $step) {
                                    if ($step->status_produksi == '1') {
                                        if (!empty($step->mesin_nama))
                                            $activeLocations[] = $step->mesin_nama;
                                        if (!empty($step->lokasi_proses))
                                            $activeLocations[] = $step->lokasi_proses;
                                    }
                                }
                            }
                            $pct = ($totalSteps > 0) ? round(($doneSteps / $totalSteps) * 100) : 0;
                            $locHint = !empty($activeLocations) ? implode(', ', array_unique($activeLocations)) : '';
                            ?>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $pct ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1" style="font-size: 0.6rem;">
                                <span class="text-primary fw-bold text-uppercase" style="white-space: normal; max-width: 100px;"><?= $locHint ?></span>
                                <span class="text-muted"><?= $pct ?>%</span>
                            </div>
                        <?php endif; ?>
                    </td>

                    <!-- Tindakan -->
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1 flex-wrap" style="min-width: 150px;">
                            <?php
                            $simpleItems = [];
                            foreach ($group_items as $itm) {
                                $simpleItems[] = [
                                    'id' => $itm->produksi_id,
                                    'nama' => $itm->product_nama,
                                    'qty' => $itm->qty,
                                    'status' => $itm->status_produksi,
                                    'dimensi' => ($itm->panjang && $itm->lebar) ? ($itm->panjang . 'x' . $itm->lebar) : ''
                                ];
                            }
                            $itemsJson = htmlspecialchars(json_encode($simpleItems), ENT_QUOTES, 'UTF-8');
                            ?>

                            <a href="<?= site_url('wip/control_center/' . $p->produksi_id) ?>"
                                class="btn btn-sm btn-dark rounded-pill px-3 fw-bold shadow-sm"
                                title="Buka Detail SPK & Item">
                                <i class="fas fa-microchip me-1"></i> Control
                            </a>

                            <?php if ($adaPending): ?>
                            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1"
                                onclick="prosesProduksiSPK('<?= $p->nospk ?>', '<?= $p->kategori_id ?>')">
                                <i class="fas fa-play"></i> Mulai
                            </button>
                            <?php elseif ($adaProses): ?>
                                <?php if ($allItemsFinalStep): ?>
                                    <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1"
                                        onclick="selesaiProduksiSPK('<?= $p->nospk ?>', '<?= $p->kategori_id ?>')">
                                        <i class="fas fa-check-double"></i> Selesai
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-info btn-sm text-white rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1"
                                        onclick="prosesProduksiSPK('<?= $p->nospk ?>', '<?= $p->kategori_id ?>')">
                                        <i class="fas fa-forward"></i> Update
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>

                            <button class="btn btn-sm btn-outline-danger rounded-pill px-2 fw-bold"
                                data-items="<?= $itemsJson ?>" onclick="laporRijekSPK('<?= $p->nospk ?>', this)"
                                title="Lapor Masalah / Rijek">
                                <i class="fas fa-exclamation-triangle"></i>
                            </button>

                            <?php if ($isReadyToPB): ?>
                                <a href="<?= site_url('wip/kirim_pb_spk/' . $p->nospk) ?>"
                                    class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark shadow-sm d-flex align-items-center justify-content-center gap-1"
                                    onclick="return confirm('Kirim ke PB?')">
                                    <i class="fas fa-truck"></i> PB
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>

                    <td class="d-none">
                        <?php
                        if ($isReadyToPB)
                            echo "#READY_PB# ";
                        if ($adaProses)
                            echo "#PROSES# ";
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>