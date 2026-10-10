<div class="table-responsive">
    <?php if (!isset($mesin_printing_map))
        $mesin_printing_map = []; ?>
    <table id="<?= $table_id ?? 'tableItemsProduksi' ?>"
        class="table premium-table table-hover align-middle mb-0 w-100 border-0" style="table-layout: auto;">
        <thead>
            <tr style="background: #fdfdfd; border-bottom: 2px solid #f1f5f9;">
                <th class="text-center <?= (isset($view_mode) && $view_mode === 'finishing') ? 'd-none' : '' ?>"
                    style="width: 40px; min-width: 40px; max-width: 40px;">
                    <div class="form-check d-flex justify-content-center m-0 p-0">
                        <input class="form-check-input check-all-nesting" type="checkbox"
                            style="cursor: pointer; width: 1.1rem; height: 1.1rem; margin-left: 0;">
                    </div>
                </th>
                <th class="text-center" width="40">No.</th>
                <th width="140">Pesanan (SPK)</th>
                <th width="150">Pelanggan & Asal</th>
                <th>Produk</th>
                <th width="200" class="text-center">Status Pesanan</th>
                <th width="120" class="text-center">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Helper to get Machine ID/Name from metadata
            $getMachineInfo = function ($item) use ($mesin_printing_map) {
                $meta = !empty($item->metadata) ? json_decode($item->metadata, true) : [];
                $merged_schemas = $item->merged_schema_assoc ?? [];

                $is_dtg = ($item->kategori_id == 8 || ($item->kategori_id == 5 && stripos($item->product_nama, 'DTG') !== false));

                // 1. DTF & Textile (Kategori 9, 5) Uses Roll Width instead of Machined ID (exclude DTG)
                if (in_array($item->kategori_id, [5, 9]) && !$is_dtg) {
                    $widthCode = $meta['ket_1'] ?? '0';
                    $wName = 'TIDAK DIKETAHUI';
                    if ($widthCode !== '0' && isset($merged_schemas['ket_1']['options'][$widthCode])) {
                        $wName = $merged_schemas['ket_1']['options'][$widthCode];
                    }
                    $formatted_wName = str_replace(',', ' x', $wName);
                    return ['id' => 'W' . $widthCode, 'name' => strtoupper($formatted_wName), 'is_dtf' => true, 'raw_width' => strtoupper($wName)];
                }

                // 2. Flatbed & Rotary (Kategori 3) Uses jenisprint_id as COLOR profiling
                $mid = $meta['jenisprint_id'] ?? '0'; // Default to '0' if undefined
                if ($item->kategori_id == 3) {
                    $cName = 'WARNA CUSTOM/NONE';
                    if ($mid !== '0' && isset($merged_schemas['jenisprint_id']['options'][$mid])) {
                        $cName = $merged_schemas['jenisprint_id']['options'][$mid];
                    }
                    return ['id' => 'C' . $mid, 'name' => 'WARNA: ' . strtoupper($cName), 'is_dtf' => false];
                }

                // 3. Laser Cut & Engrave (Kategori 6) uses ket_1 as MATERIAL grouping
                if ($item->kategori_id == 6) {
                    $mCode = $meta['ket_1'] ?? '0';
                    $mName = 'MATERIAL TIDAK DIKETAHUI';
                    if ($mCode !== '0' && isset($merged_schemas['ket_1']['options'][$mCode])) {
                        $mName = $merged_schemas['ket_1']['options'][$mCode];
                    }
                    $thick = $meta['thickness'] ?? '0';
                    $thickStr = ($thick !== '0') ? ' (' . $thick . 'mm)' : '';
                    return ['id' => 'L' . $mCode . 'T' . $thick, 'name' => 'MATERIAL: ' . strtoupper($mName) . $thickStr, 'is_dtf' => false];
                }

                // 4. Apparel (Kategori 7) uses model_apparel + lengan grouping
                if ($item->kategori_id == 7) {
                    $mCode = $meta['model_apparel'] ?? '0';
                    $lCode = $meta['lengan'] ?? '0';
                    $mName = 'APPAREL / MODEL UNKNOWN';
                    if ($mCode !== '0' && isset($merged_schemas['model_apparel']['options'][$mCode])) {
                        $mName = $merged_schemas['model_apparel']['options'][$mCode];
                    }
                    $lName = '';
                    if ($lCode !== '0' && isset($merged_schemas['lengan']['options'][$lCode])) {
                        $lName = ' (' . $merged_schemas['lengan']['options'][$lCode] . ')';
                    }
                    return ['id' => 'A' . $mCode . 'L' . $lCode, 'name' => strtoupper($mName . $lName), 'is_dtf' => false];
                }

                // 5. DTG (Kategori 8 or DTG in Kategori 5)
                if ($is_dtg) {
                    $mid = $meta['ket_1'] ?? $meta['jenisprint_id'] ?? '0';
                    $pName = 'DTG / Sablon Digital';
                    if ($mid !== '0' && isset($merged_schemas['ket_1']['options'][$mid])) {
                        $pName = $merged_schemas['ket_1']['options'][$mid];
                    } elseif ($mid !== '0' && isset($merged_schemas['jenisprint_id']['options'][$mid])) {
                        $pName = $merged_schemas['jenisprint_id']['options'][$mid];
                    }
                    return ['id' => 'D' . $mid, 'name' => 'UKURAN/PROSES: ' . strtoupper($pName), 'is_dtf' => false];
                }

                // 6. Merchandise (Kategori 10) uses ket_1 as MODEL/VARIAN grouping
                if ($item->kategori_id == 10) {
                    $mCode = $meta['ket_1'] ?? '0';
                    $mName = 'STANDAR / TANPA VARIAN';
                    if ($mCode !== '0' && isset($merged_schemas['ket_1']['options'][$mCode])) {
                        $mName = $merged_schemas['ket_1']['options'][$mCode];
                    }
                    return ['id' => 'M' . $mCode, 'name' => 'MODEL: ' . strtoupper($mName), 'is_dtf' => false];
                }

                // 7. Normal LF Logic
                if (isset($item->tipe_id) && $item->tipe_id == 2) {
                    return ['id' => 'FINISH', 'name' => 'BARANG JADI / FINISHING', 'is_dtf' => false];
                }

                $mName = 'Standard / Unspecified';
                if ($mid !== '0') {
                    if (isset($merged_schemas['jenisprint_id']['options'][$mid])) {
                        $mName = strtoupper($merged_schemas['jenisprint_id']['options'][$mid]);
                    }
                }
                return ['id' => $mid, 'name' => strtoupper($mName), 'is_dtf' => false];
            };

            // Determine group mode (bahan = default, spk = group by SPK)
            $group_mode = $group_mode ?? 'bahan';
            $filter_status_view = $filter_status_view ?? $_GET['status_view'] ?? 'pending';

            if (!empty($raw_items)) {
                // 1. Group items
                $grouped_for_sort = [];
                foreach ($raw_items as $item) {
                    $mInfo = $getMachineInfo($item);
                    $bName = $item->bom->bahan_nama ?? 'Tanpa Bahan';

                    if ($group_mode === 'spk') {
                        $gk = 'SPK|' . $item->nospk . '|' . ($item->konsumen_nama ?? '');
                    } else {
                        $gk = $mInfo['id'] . '|' . $bName;
                    }
                    if (!isset($grouped_for_sort[$gk])) {
                        $grouped_for_sort[$gk] = [
                            'max_time' => 0,
                            'max_id' => 0,
                            'items' => []
                        ];
                    }

                    // Determine which timestamp to use for group sorting
                    if ($filter_status_view === 'ready_pb' || $filter_status_view === 'proses') {
                        $ts = !empty($item->update_time) ? strtotime($item->update_time) : 0;
                    } elseif ($filter_status_view === 'pb') {
                        $ts = !empty($item->tgl_kirim_pb) ? strtotime($item->tgl_kirim_pb) : 0;
                    } else {
                        $ts = !empty($item->tgl_spk) ? strtotime($item->tgl_spk) : 0;
                    }

                    if ($ts > $grouped_for_sort[$gk]['max_time']) {
                        $grouped_for_sort[$gk]['max_time'] = $ts;
                    }
                    if ((int) $item->produksi_id > $grouped_for_sort[$gk]['max_id']) {
                        $grouped_for_sort[$gk]['max_id'] = (int) $item->produksi_id;
                    }

                    $grouped_for_sort[$gk]['items'][] = $item;
                }

                // 2. Sort groups (Always sort by time/id to bring the latest active groups to the top)
                uasort($grouped_for_sort, function ($a, $b) {
                    if ($a['max_time'] !== $b['max_time']) {
                        return $b['max_time'] <=> $a['max_time'];
                    }
                    return $b['max_id'] <=> $a['max_id'];
                });

                // 3. Flatten back to raw_items
                $raw_items = [];
                foreach ($grouped_for_sort as $gk => $groupData) {
                    // Inside the group, sort by ID descending just to be clean
                    usort($groupData['items'], function ($a, $b) {
                        return (int) $b->produksi_id - (int) $a->produksi_id;
                    });
                    foreach ($groupData['items'] as $item) {
                        $raw_items[] = $item;
                    }
                }
            }

            // Pre-calculate Group Metadata for Smart Actions
            $groupStates = [];
            $check_op_global = (isset($active_operator) && $active_operator !== 'all') ? (int)$active_operator : (int)($current_operator_id ?? 0);

            if (!empty($raw_items)) {
                foreach ($raw_items as $itm) {
                    $mInfo = $getMachineInfo($itm);
                    $bName = $itm->bom->bahan_nama ?? 'Tanpa Bahan';
                    if ($group_mode === 'spk') {
                        $gk = 'SPK|' . $itm->nospk . '|' . ($itm->konsumen_nama ?? '');
                    } else {
                        $gk = $mInfo['id'] . '|' . $bName;
                    }

                    if (!isset($groupStates[$gk])) {
                        $groupStates[$gk] = [
                            'pending' => 0, 
                            'proses' => 0, 
                            'done' => 0,
                            'auth_pending' => 0,
                            'auth_proses' => 0,
                            'auth_done' => 0
                        ];
                    }
                    $stKey = (string) $itm->status_produksi;

                    // Determine active and last step operators for this item
                    $active_step_operator_id = 0;
                    $last_step_operator_id = 0;
                    if (!empty($itm->routing_steps)) {
                        $last_step = end($itm->routing_steps);
                        $last_step_operator_id = (int)($last_step->routing_operator_id ?? 0);
                        reset($itm->routing_steps);
                        
                        foreach ($itm->routing_steps as $step) {
                            if ($step->status_produksi == '1' || $step->status_produksi == '0') {
                                $active_step_operator_id = (int)$step->routing_operator_id;
                                break;
                            }
                        }
                    }

                    $is_auth_active = ($check_op_global === 0 || $check_op_global === $active_step_operator_id);
                    $is_auth_pb = ($check_op_global === 0 || $check_op_global === $last_step_operator_id);

                    if (in_array($stKey, ['approved', 'pending', '', '0'])) {
                        $groupStates[$gk]['pending']++;
                        if ($is_auth_active) $groupStates[$gk]['auth_pending']++;
                    }
                    elseif (in_array($stKey, ['proses', 'finishing'], true)) {
                        $groupStates[$gk]['proses']++;
                        if ($is_auth_active) $groupStates[$gk]['auth_proses']++;
                    }
                    elseif ($stKey === 'done') {
                        $groupStates[$gk]['done']++;
                        if ($is_auth_pb) $groupStates[$gk]['auth_done']++;
                    }
                }
            }

            $noItem = 1;
            $lastGroupKey = null;

            foreach ($raw_items as $item):
                $curBahan = $item->bom->bahan_nama ?? 'Tanpa Bahan';
                $curMachine = $getMachineInfo($item);

                // Composite Key for Grouping: depends on group_mode
                if ($group_mode === 'spk') {
                    $currentGroupKey = 'SPK|' . $item->nospk . '|' . ($item->konsumen_nama ?? '');
                } else {
                    $currentGroupKey = $curMachine['id'] . '|' . $curBahan;
                }
                $groupChanged = ($currentGroupKey !== $lastGroupKey);
                $lastGroupKey = $currentGroupKey;

                // Determine Active Step and Operator Authorization
                $active_step_operator_id = 0;
                $active_step_name = '';
                $active_step_status = '';
                if (!empty($item->routing_steps)) {
                    foreach ($item->routing_steps as $step) {
                        if ($step->status_produksi == '1' || $step->status_produksi == '0') {
                            $active_step_operator_id = (int)$step->routing_operator_id;
                            $active_step_name = strtoupper($step->routing_name);
                            $active_step_status = (string)$step->status_produksi;
                            break;
                        }
                    }
                }
                $is_sa = $is_super_admin ?? false;
                $curr_op = (int)($current_operator_id ?? 0);
                $view_op = (isset($active_operator) && $active_operator !== 'all') ? (int)$active_operator : 0;
                
                // PIPELINE VISIBILITY CHECK
                $is_waiting_upstream = false;
                $check_op = $view_op !== 0 ? $view_op : $curr_op;

                if ($check_op !== 0 && $active_step_operator_id !== 0 && $check_op !== $active_step_operator_id) {
                    $is_waiting_upstream = true;
                }

                // OPERATOR ACCESS POLICY: Show buttons to everyone in the same department.
                // Security and accountability are handled by PIN verification at the time of clicking.
                $can_execute = true;

                $st = (string) $item->status_produksi;
                $stClass = 'bg-info bg-opacity-10 text-info border-info';
                if (in_array($st, ['proses', 'finishing'], true))
                    $stClass = 'bg-warning bg-opacity-10 text-warning border-warning';
                if ($st === 'done')
                    $stClass = 'bg-success bg-opacity-10 text-success border-success';
                if ($st === 'on_pb')
                    $stClass = 'bg-primary bg-opacity-10 text-primary border-primary';
                ?>
                <?php if ($groupChanged):
                    $gMeta = $groupStates[$currentGroupKey] ?? ['pending' => 0, 'proses' => 0, 'done' => 0, 'auth_pending' => 0, 'auth_proses' => 0, 'auth_done' => 0];
                    $hasProses = ($gMeta['proses'] > 0);
                    $hasPending = ($gMeta['pending'] > 0);
                    ?>
                    <tr class="tr-group-header group-header" data-kategori="<?= $item->kategori_id ?>">
                        <td colspan="<?= (isset($view_mode) && $view_mode === 'finishing') ? '11' : '12' ?>"
                            class="p-0 border-0">
                            <div
                                class="d-flex align-items-center justify-content-between bg-primary bg-opacity-10 py-1 px-4 border-start border-4 border-primary mt-3 mb-1 shadow-sm rounded-end">
                                <div class="d-flex align-items-center gap-3">
                                    <?php $is_spk_group = (strpos($currentGroupKey, 'SPK|') === 0); ?>
                                    <?php if ($is_spk_group): ?>
                                        <div
                                            class="d-flex align-items-center gap-2 px-3 py-1 bg-white border border-primary border-opacity-25 rounded-pill shadow-sm">
                                            <i class="fas fa-file-invoice text-primary" style="font-size: 0.8rem;"></i>
                                            <span class="fw-900 text-primary"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px;">SPK: <?= $item->nospk ?></span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-user text-dark opacity-50"></i>
                                            <span class="fw-900 text-dark opacity-75"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;"><?= strtoupper($item->konsumen_nama ?? 'Tanpa Konsumen') ?></span>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($curMachine['id'] !== '0'): ?>
                                            <div
                                                class="d-flex align-items-center gap-2 px-3 py-1 bg-white border border-primary border-opacity-25 rounded-pill shadow-sm">
                                                <i class="fas fa-microchip text-primary" style="font-size: 0.8rem;"></i>
                                                <span class="fw-900 text-primary"
                                                    style="font-size: 0.65rem; letter-spacing: 0.5px;"><?= $curMachine['name'] ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-layer-group text-dark opacity-50"></i>
                                            <span class="fw-900 text-dark opacity-75"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;"><?= $curBahan ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Stats Mini -->
                                    <div class="ms-2 d-flex gap-2">
                                        <?php if ($gMeta['pending'] > 0): ?>
                                            <span class="badge bg-warning text-dark opacity-75 fw-bold"
                                                style="font-size: 0.55rem;"><?= $gMeta['pending'] ?> ANTRIAN</span>
                                        <?php endif; ?>
                                        <?php if ($gMeta['proses'] > 0): ?>
                                            <span class="badge bg-primary opacity-75 fw-bold"
                                                style="font-size: 0.55rem;"><?= $gMeta['proses'] ?> SEDANG PROSES</span>
                                        <?php endif; ?>
                                        <?php if ($gMeta['done'] > 0): ?>
                                            <span class="badge bg-success opacity-75 fw-bold"
                                                style="font-size: 0.55rem;"><?= $gMeta['done'] ?> SIAP KIRIM</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 flex-wrap justify-content-end">
                                    <?php if ($can_execute): ?>
                                        <?php if ($gMeta['auth_done'] > 0): ?>
                                            <button type="button"
                                                class="btn btn-outline-success fw-900 rounded-pill px-4 py-2 shadow-sm"
                                                style="font-size: 0.75rem;"
                                                onclick="ProductionApp.sendGroupToPB('<?= htmlspecialchars(addslashes($currentGroupKey), ENT_QUOTES, 'UTF-8') ?>')">
                                                <i class="fas fa-truck-loading me-1"></i> KE PB (<?= $gMeta['auth_done'] ?>)
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($gMeta['auth_proses'] > 0): ?>
                                            <button type="button"
                                                class="btn btn-success fw-900 rounded-pill px-4 py-2 shadow-sm animate__animated animate__pulse animate__infinite"
                                                style="font-size: 0.75rem;"
                                                onclick="ProductionApp.finishGroupBatch('<?= htmlspecialchars(addslashes($currentGroupKey), ENT_QUOTES, 'UTF-8') ?>')">
                                                <i class="fas fa-check-double me-1"></i>
                                                <?= (isset($view_mode) && $view_mode === 'finishing') ? 'SELESAIKAN BATCH' : 'SELESAIKAN BATCH' ?>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($gMeta['auth_pending'] > 0): ?>
                                            <?php if (isset($view_mode) && $view_mode === 'finishing'): ?>
                                                <button type="button" class="btn btn-primary fw-900 rounded-pill px-4 py-2 shadow-sm"
                                                    style="font-size: 0.75rem;"
                                                    onclick="ProductionApp.startGroupBatch('<?= htmlspecialchars(addslashes($currentGroupKey), ENT_QUOTES, 'UTF-8') ?>')">
                                                    <i class="fas fa-play me-1"></i> MULAI BATCH
                                                </button>
                                            <?php else: ?>
                                                <?php 
                                                    // Hanya tampilkan tombol "Mulai Semua" jika kategori BUKAN A3+ (1) atau Large Format (2, 3) 
                                                    // Cocok untuk Textile (5), Merchandise (10), DTF (6), Finishing (16)
                                                    $allowed_start_all = !in_array($item->kategori_id, [1, 2, 3, 4, 8, 9]); 
                                                ?>
                                                <?php if ($is_spk_group && $allowed_start_all): ?>
                                                    <button type="button"
                                                        class="btn btn-success fw-900 rounded-pill px-4 py-2 shadow-sm me-2"
                                                        style="font-size: 0.75rem;"
                                                        title="Mulai semua item dalam SPK ini tanpa proses Ripping (Cocok untuk Textile/Jasa)"
                                                        onclick="ProductionApp.startSPKBatch('<?= htmlspecialchars(addslashes($currentGroupKey), ENT_QUOTES, 'UTF-8') ?>')">
                                                        <i class="fas fa-play me-1"></i> MULAI SEMUA DI SPK INI
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button"
                                                    class="btn btn-<?= ($gMeta['auth_proses'] > 0) ? 'outline-primary' : 'primary' ?> fw-900 rounded-pill px-4 py-2 shadow-sm"
                                                    style="font-size: 0.75rem;"
                                                    onclick="selectAllByMaterial('<?= htmlspecialchars(addslashes($currentGroupKey), ENT_QUOTES, 'UTF-8') ?>')">
                                                    <i class="fas fa-magic me-1"></i> <?= ($gMeta['auth_proses'] > 0) ? 'TAMBAH RIPPING' : 'PILIH RIPPING' ?>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php
                $meta = !empty($item->metadata) ? json_decode($item->metadata, true) : [];
                $p = (float) ($item->panjang ?: ($meta['panjang'] ?? 0));
                $l = (float) ($item->lebar ?: ($meta['lebar'] ?? 0));
                $qty = (float) $item->qty;
                $satuan_nama = strtoupper(trim($item->satuan_nama ?? ''));

                // Kalkulasi Area & Label based on Category and Satuan
                if ($item->kategori_id == 9) {
                    $areaRaw = ($p * $qty);
                    $dimUnit = 'METER';
                    $areaLabel = 'METER LARI';
                } elseif ($item->kategori_id == 5 && !$is_dtg) {
                    // Textile: lebar disimpan di ket_1 (e.g. "110 CM"), bukan di kolom lebar transaksi
                    $lebarM = $l; // gunakan lebar transaksi jika ada
                    if ($lebarM <= 0) {
                        $ket1_val = $meta['ket_1'] ?? $item->db_ket_1 ?? '';
                        // Ekstrak angka dari string seperti "110 CM" atau "110CM" atau "1.1"
                        if (preg_match('/[\d]+(?:[.,]\d+)?/', str_replace(',', '.', $ket1_val), $m_w)) {
                            $lebarCm = (float) $m_w[0];
                            $lebarM = $lebarCm / 100; // konversi CM → M
                        }
                    }
                    $areaRaw = ($p * $lebarM * $qty);
                    $dimUnit = 'METER';
                    $areaLabel = 'TOTAL M²';
                } elseif ($is_dtg) {
                    $areaRaw = $qty; // Default area to qty for DTG
                    $dimUnit = 'PCS';
                    $areaLabel = 'QTY';
                } elseif (in_array($item->kategori_id, [3, 6]) || ($item->kategori_id == 10 && $p > 0 && $l > 0)) {
                    // Jika satuan gudang adalah CM/CM2, jangan dibagi 10000
                    if ($satuan_nama === 'CM' || $satuan_nama === 'CM2') {
                        $areaRaw = ($p * $l * $qty);
                        $dimUnit = 'CM';
                        $areaLabel = 'TOTAL CM²';
                    } else {
                        $areaRaw = ($p * $l * $qty) / 10000;
                        $dimUnit = 'CM';
                        $areaLabel = 'TOTAL M²';
                    }
                } elseif (in_array($item->kategori_id, [7, 8, 10])) {
                    $areaRaw = $qty;
                    $dimUnit = 'UNIT';
                    $areaLabel = 'TOTAL PCS';
                } else {
                    $areaRaw = ($p * $l * $qty);
                    $dimUnit = 'METER';
                    $areaLabel = 'TOTAL M²';
                }
                ?>
                <?php
                $isReprintItem = (!empty($item->is_reprint) || strpos(($item->catatan ?? ''), '[RIJEK]') !== false);
                
                $itemTitle = $item->product_nama;
                if (!empty($meta) && is_array($meta)) {
                    $fin_parts = [];
                    if (!empty($meta['finishing'])) $fin_parts[] = $meta['finishing'];
                    if (!empty($meta['ukuran'])) $fin_parts[] = $meta['ukuran'];
                    if (!empty($fin_parts)) {
                        $itemTitle .= ' [' . implode(', ', $fin_parts) . ']';
                    }
                } elseif (!empty($item->db_ket_1)) {
                    $itemTitle .= ' [' . $item->db_ket_1 . ']';
                }
                ?>
                <tr class="tr-item-row tr-nesting-item <?= $isReprintItem ? 'tr-reprint' : '' ?>"
                    style="border-left: 5px solid <?= $isReprintItem ? '#e74c3c' : 'transparent' ?>; transition: all 0.2s ease;"
                    data-kategori="<?= $item->kategori_id ?>" data-material="<?= htmlspecialchars($currentGroupKey) ?>"
                    data-nospk="<?= $item->nospk ?>">
                    <td style="width: 1%; white-space: nowrap;"
                        class="text-center <?= (isset($view_mode) && $view_mode === 'finishing') ? 'd-none' : '' ?>">
                        <div class="form-check d-flex justify-content-center m-0 p-0">
                            <?php
                            $filtered_bom_lines = !empty($item->bom_lines) ? $item->bom_lines : (!empty($item->bom) ? [$item->bom] : []);
                            $current_step_bom_line_id = $item->current_step_bom_line_id ?? null;
                            $active_bom = null;

                            if ($current_step_bom_line_id) {
                                foreach ($filtered_bom_lines as $b) {
                                    if ($b->bom_line_id == $current_step_bom_line_id) {
                                        $active_bom = $b;
                                        break;
                                    }
                                }
                            }
                            if (!$active_bom) {
                                $active_bom = $item->bom ?? ($filtered_bom_lines[0] ?? null);
                            }
                            ?>
                            <input class="form-check-input check-item-nesting" type="checkbox"
                                data-id="<?= $item->produksi_id ?>" data-panjang="<?= $p ?>" data-lebar="<?= $l ?>"
                                data-qty="<?= $qty ?>" data-bahan-id="<?= $active_bom->bahan_id ?? 0 ?>"
                                data-bom-material-id="<?= $active_bom->bom_material_id ?? 0 ?>"
                                data-bom-p="<?= (float) ($active_bom->panjang ?? 0) ?>"
                                data-bom-l="<?= (float) ($active_bom->lebar ?? 0) ?>"
                                data-bom-qty="<?= (float) ($active_bom->quantity ?? $active_bom->bom_qty ?? 1) ?>"
                                data-status="<?= $item->status_produksi ?>" data-kategori-id="<?= $item->kategori_id ?>"
                                data-tipe-id="<?= $item->tipe_id ?? 1 ?>"
                                data-satuan="<?= htmlspecialchars($active_bom->satuan_nama ?? 'UNIT') ?>"
                                data-satuan-produk="<?= htmlspecialchars($item->satuan_nama ?? 'UNIT') ?>"
                                data-step-urutan="<?= $item->current_step_urutan ?? 1 ?>"
                                data-step-name="<?= htmlspecialchars($item->current_step_name ?? 'FINISHING') ?>"
                                data-step-id="<?= htmlspecialchars($item->workflow_step_id ?? '') ?>"
                                data-step-version="<?= htmlspecialchars($item->workflow_version ?? '') ?>"
                                data-step-output="<?= htmlspecialchars($item->workflow_qty_output ?? 0) ?>"
                                data-current-bom-line-id="<?= htmlspecialchars($current_step_bom_line_id ?? '') ?>"
                                data-bom-lines="<?= htmlspecialchars(json_encode($filtered_bom_lines), ENT_QUOTES, 'UTF-8') ?>"
                                data-ripping-group="<?= htmlspecialchars($item->ripping_group ?? '') ?>"
                                data-item-title="<?= htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8') ?>"
                                style="width: 1.1rem; height: 1.1rem; cursor: pointer;" <?= ($item->status_produksi === 'done' || !$can_execute || $is_waiting_upstream) ? 'disabled' : '' ?>>
                        </div>
                    </td>
                    <td class="text-center fw-semibold text-muted" style="vertical-align: middle; font-size: 0.95rem;">
                        <?= $noItem++ ?>
                    </td>
                    <!-- Pesanan (SPK) -->
                    <td style="vertical-align: top; padding-top: 0.75rem;">
                        <div class="d-flex flex-column gap-1">
                            <div class="spk-id-text text-nowrap mb-1">
                                <a href="javascript:void(0)" onclick="previewSPK('<?= $item->nospk ?>')" class="text-decoration-none text-primary" title="Lihat Detail SPK">
                                    <?= $item->nospk ?>
                                </a>
                            </div>
                            <?php if (!empty($item->is_reprint) || strpos(($item->catatan ?? ''), '[RIJEK]') !== false): ?>
                                <span class="badge bg-danger rounded-pill pulse-red" style="padding: 2px 6px; font-size: 0.55rem; width: fit-content;"><i class="fas fa-redo me-1"></i>REPRINT</span>
                            <?php endif; ?>
                            
                            <?php
                            // Date calculation already exists in code above this, but we'll re-declare just in case it's needed locally
                            $datetime = new DateTime($item->tgl_spk);
                            $sekarang = new DateTime();
                            $hari_nama = [
                                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                            ][$datetime->format('l')];
                            $bulan_nama = [
                                'January' => 'Jan', 'February' => 'Feb', 'March' => 'Mar', 'April' => 'Apr',
                                'May' => 'Mei', 'June' => 'Jun', 'July' => 'Jul', 'August' => 'Ags',
                                'September' => 'Sep', 'October' => 'Okt', 'November' => 'Nov', 'December' => 'Des'
                            ][$datetime->format('F')];

                            $tanggal = $datetime->format('d');
                            $jam = $datetime->format('H:i');
                            
                            $kemarin = clone $sekarang;
                            $kemarin->modify('-1 day');
                            
                            $tanggal_str = $datetime->format('Y-m-d');
                            $is_today = $tanggal_str === $sekarang->format('Y-m-d');
                            $is_yesterday = $tanggal_str === $kemarin->format('Y-m-d');
                            ?>

                            <div class="small" style="font-size: 0.75rem;">
                                <div class="text-dark d-flex flex-column gap-1">
                                    <span><i class="far fa-calendar-alt text-muted"></i> <?= $hari_nama ?>, <?= $tanggal ?> <?= $bulan_nama ?></span>
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="far fa-clock text-muted"></i> <strong><?= $jam ?></strong> <span class="fw-bold text-primary" style="font-size: 0.65rem;">WIB</span>
                                    </span>
                                </div>
                                <?php if ($is_today): ?>
                                    <span class="badge badge-hari-ini text-uppercase mt-1" style="font-size: 0.55rem;"><i class="fas fa-calendar-day"></i> HARI INI</span>
                                <?php elseif ($is_yesterday): ?>
                                    <span class="badge badge-kemarin text-uppercase mt-1" style="font-size: 0.55rem;"><i class="fas fa-calendar"></i> KEMARIN</span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($item->dateline_tgl) && $item->dateline_tgl != '0000-00-00'): ?>
                                <div class="text-danger fw-bold mt-1" style="font-size: 0.75rem;"><i class="fas fa-hourglass-half me-1"></i> <?= date('d/m/Y', strtotime($item->dateline_tgl)) ?> <?= !empty($item->dateline_jam) ? date('H:i', strtotime($item->dateline_jam)) : '' ?></div>
                            <?php endif; ?>

                            <?php if (isset($filter_status_view) && $filter_status_view === 'konsumen'): ?>
                                <div class="mt-2 text-center">
                                    <span class="badge bg-secondary text-white shadow-sm border border-secondary border-opacity-25" style="font-size: 0.65rem; padding: 3px 6px;">
                                        <i class="fas fa-barcode me-1"></i> <?= htmlspecialchars($item->produksi_id) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>

                    <!-- Pelanggan & Asal -->
                    <td style="vertical-align: top; padding-top: 0.75rem;">
                        <div class="d-flex flex-column gap-2">
                            <div class="fw-bold text-dark text-uppercase"
                                style="font-size: 0.85rem; letter-spacing: 0.3px;"><?= $item->konsumen_nama ?></div>
                            <?php if (!empty($item->konsumen_nohp)): ?>
                                <div class="text-muted small d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                    <i class="fab fa-whatsapp text-success" style="font-size: 0.8rem;"></i>
                                    <span><?= $item->konsumen_nohp ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <?php if (!empty($item->nama_jenis_transaksi)): ?>
                                    <?php 
                                        $is_marketplace = (strtoupper($item->nama_jenis_transaksi) === 'MARKETPLACE' && !empty($item->marketplace_nama));
                                        $display_metode = $is_marketplace ? $item->marketplace_nama : $item->nama_jenis_transaksi;
                                        $icon = 'fa-shopping-cart';
                                        $color = 'secondary';
                                        
                                        if ($is_marketplace) {
                                            $icon = 'fa-store';
                                            $mp_lower = strtolower($item->marketplace_nama);
                                            if (strpos($mp_lower, 'shopee') !== false) {
                                                $color = 'danger';
                                            } else if (strpos($mp_lower, 'tokopedia') !== false) {
                                                $color = 'success';
                                            } else if (strpos($mp_lower, 'tiktok') !== false) {
                                                $color = 'dark';
                                            } else {
                                                $color = 'warning';
                                            }
                                        }
                                    ?>
                                    <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> border border-<?= $color ?> border-opacity-25 shadow-sm fw-900" style="font-size: 0.65rem;">
                                        <i class="fas <?= $icon ?> me-1"></i> <?= strtoupper($display_metode) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-1">
                                <?php
                                $comp_name = $item->nama_perusahaan ?: 'N/A';
                                $hash = array_sum(array_map('ord', str_split($comp_name)));
                                $branchColors = ['#2196f3', '#9c27b0', '#4caf50', '#ff9800', '#e91e63', '#8bc34a', '#009688', '#ffc107', '#673ab7', '#3f51b5'];
                                $branchColor = $branchColors[$hash % count($branchColors)];
                                ?>
                                <div class="company-pill"
                                    style="color: <?= $branchColor ?>; border: 1px solid <?= $branchColor ?>20; font-size: 0.65rem;">
                                    <div class="company-dot" style="background: <?= $branchColor ?>"></div>
                                    <?= $comp_name ?>
                                </div>
                                <?php if (!empty($item->lokasi_cetak) && $item->lokasi_cetak != 0 && $item->perusahaan_id != $item->lokasi_cetak): ?>
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill pulse-red mt-1"
                                        style="padding: 3px 6px; font-size: 0.55rem; display: block; width: fit-content;">
                                        <i class="fas fa-exchange-alt me-1"></i>TITIPAN DARI
                                        <?= strtoupper($item->nama_perusahaan ?? 'CABANG LAIN') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <td style="vertical-align: top; padding-top: 0.75rem; padding-bottom: 0.75rem;">
                        <div class="d-flex flex-column gap-1">
                            <!-- 1. Header Row (Product Name + BOM Button + URGENT) -->
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <div>
                                    <?php if ($is_urgent_item): ?>
                                        <span class="badge bg-danger rounded-pill pulse-red text-uppercase mb-1" style="padding: 3px 6px; font-size: 0.55rem;"><i class="fas fa-bolt me-1"></i>URGENT</span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($view_mode) && $view_mode == 'finishing'): ?>
                                        <div class="fw-bold text-success fs-6 ls-n1 line-height-1 mb-1">
                                            <?php
                                            $finishing_text = strtoupper($item->current_step_name ?: 'FINISHING');
                                            if (!empty($item->current_step_name) && preg_match('/\((.*)/', $item->current_step_name, $matches)) {
                                                $finishing_text = strtoupper(trim(str_replace(')', '', $matches[1])));
                                            }
                                            echo '<i class="fas fa-tools me-1"></i> ' . $finishing_text;
                                            ?>
                                        </div>
                                        <div class="text-muted fw-bold" style="font-size: 0.75rem;">Dari: <?= $item->product_nama ?></div>
                                    <?php else: ?>
                                        <div class="fw-bold text-dark fs-6 ls-n1 lh-sm"><?= $item->product_nama ?></div>
                                        <?php
                                        $finishing_steps = [];
                                        if (!empty($item->routing_steps)) {
                                            foreach ($item->routing_steps as $step) {
                                                $op_id = (int) $step->routing_operator_id;
                                                $is_fin = false;
                                                if (in_array($op_id, [2, 4, 7])) {
                                                    $is_fin = true;
                                                } else {
                                                    $nama_lower = strtolower($step->routing_name ?: $item->product_nama);
                                                    if (
                                                        strpos($nama_lower, 'finishing') !== false ||
                                                        strpos($nama_lower, 'laminasi') !== false ||
                                                        strpos($nama_lower, 'mata ayam') !== false ||
                                                        strpos($nama_lower, 'seaming') !== false ||
                                                        strpos($nama_lower, 'potong') !== false ||
                                                        strpos($nama_lower, 'jilid') !== false ||
                                                        strpos($nama_lower, 'lipat') !== false ||
                                                        strpos($nama_lower, 'packing') !== false ||
                                                        strpos($nama_lower, 'lem') !== false ||
                                                        strpos($nama_lower, 'spiral') !== false ||
                                                        strpos($nama_lower, 'rangka') !== false ||
                                                        strpos($nama_lower, 'tiang') !== false ||
                                                        strpos($nama_lower, 'kaki') !== false ||
                                                        strpos($nama_lower, 'tripod') !== false ||
                                                        strpos($nama_lower, 'stand') !== false ||
                                                        strpos($nama_lower, 'holder') !== false ||
                                                        strpos($nama_lower, 'door frame') !== false
                                                    ) {
                                                        $is_fin = true;
                                                    }
                                                }
                                                if ($is_fin) {
                                                    $finishing_steps[] = $step;
                                                }
                                            }
                                        }
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- 2. Primary Metrics: QTY & Dimensions -->
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                <?php $uniform_badge_style = "padding: 4px 10px; font-size: 0.65rem; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; line-height: 1.2;"; ?>
                                <span class="badge bg-secondary shadow-sm" style="<?= $uniform_badge_style ?>">QTY: <?= $qty ?> PCS</span>
                                <?php if ($item->kategori_id == 5): ?>
                                    <span class="badge bg-light text-dark border" style="<?= $uniform_badge_style ?>"><?= $p ?>M x <?= htmlspecialchars($l > 0 ? $l : ($meta['ket_1'] ?? $item->db_ket_1 ?? (isset($curMachine['raw_width']) ? $curMachine['raw_width'] : '?'))) ?></span>
                                <?php elseif ($item->kategori_id == 9): ?>
                                    <span class="badge bg-light text-dark border" style="<?= $uniform_badge_style ?>">Pjg: <?= $p ?> ML</span>
                                <?php elseif ($item->panjang > 0 && $item->lebar > 0): ?>
                                    <span class="badge bg-light text-dark border" style="<?= $uniform_badge_style ?>"><?= $p ?> x <?= $l ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!isset($filter_status_view) || $filter_status_view !== 'konsumen'): ?>
                                <!-- 3. Tags / Attributes -->
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <?php if ($isReprintItem): ?>
                                        <span class="badge bg-danger shadow-sm" style="<?= $uniform_badge_style ?>"><i class="fas fa-redo me-1"></i>REPRINT / RIJEK</span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($item->ripping_group)): ?>
                                        <span class="badge bg-primary text-white shadow-sm" style="<?= $uniform_badge_style ?>">
                                            <i class="fas fa-object-group me-1"></i> RIPPING GROUP: <?= substr(strtoupper(str_replace('rip_', '', $item->ripping_group)), 0, 5) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php $this->load->view('admin/wip/components/v_render_metadata', ['item' => $item, 'category_schemas' => $category_schemas, 'only_instructions' => false, 'only_badges' => true]); ?>
                                </div>
                                
                                <!-- 4. Action Buttons & Routing Sequence -->
                                <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                                    <!-- Tag button (Yellow) -->
                                    <button type="button" class="btn btn-sm text-white shadow-sm d-flex align-items-center justify-content-center"
                                        style="background-color: #ffb703; <?= $uniform_badge_style ?> border: none;"
                                        onclick="ProductionApp.previewTag('<?= $item->produksi_id ?>')" title="Cetak Tag">
                                        <i class="fas fa-tag me-1"></i> TAG
                                    </button>

                                    <!-- BOM Button -->
                                    <?php if (!empty($item->bom_lines)): ?>
                                        <?php
                                        $bom_json_data = [];
                                        foreach ($item->bom_lines as $bl) {
                                            $base_qty = (float)$bl->bom_qty;
                                            $total_qty = $base_qty * $qty;
                                            $bom_json_data[] = [
                                                'name' => $bl->bahan_nama ?? 'Unknown',
                                                'base_qty' => $base_qty,
                                                'order_qty' => (float)$qty,
                                                'qty' => number_format($total_qty, 2),
                                                'unit' => $bl->satuan_nama ?? 'Unit'
                                            ];
                                        }
                                        ?>
                                        <button type="button"
                                            class="btn btn-sm bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 shadow-sm btn-show-bom-modal d-flex align-items-center justify-content-center"
                                            style="<?= $uniform_badge_style ?> cursor: pointer;" data-product="<?= htmlspecialchars($item->product_nama) ?>"
                                            data-bom="<?= htmlspecialchars(json_encode($bom_json_data), ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="fas fa-box-open me-1"></i> BOM
                                        </button>
                                    <?php endif; ?>

                                    <!-- Routing Sequence -->
                                    <?php if (!empty($item->routing_steps)): ?>
                                        <div class="d-flex flex-wrap align-items-center gap-1 ms-1" style="font-size: 0.6rem;">
                                            <span class="text-muted fw-bold" style="font-size: 0.65rem;">Rute Proses:</span>
                                            <?php foreach ($item->routing_steps as $idx => $step): ?>
                                                <?php 
                                                $isActive = ($step->status_produksi == '1'); 
                                                $isDone = ($step->status_produksi == '2');
                                                
                                                $bgClass = 'bg-light text-secondary border border-secondary border-opacity-25';
                                                $icon = '';
                                                if ($isDone) {
                                                    $bgClass = 'bg-success bg-opacity-75 text-white';
                                                    $icon = '<i class="fas fa-check me-1"></i>';
                                                } elseif ($isActive) {
                                                    $bgClass = 'bg-primary text-white shadow-sm';
                                                    $icon = '<i class="fas fa-cog fa-spin me-1"></i>';
                                                }
                                                ?>
                                                <span class="badge <?= $bgClass ?> fw-normal" style="<?= $uniform_badge_style ?>" title="Urutan: <?= $step->routing_urutan ?>">
                                                    <?= $icon ?><?= htmlspecialchars($step->routing_name) ?>
                                                </span>
                                                <?php if ($idx < count($item->routing_steps) - 1): ?>
                                                    <i class="fas fa-caret-right text-muted opacity-50" style="font-size: 0.65rem;"></i>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- 5. Instructions -->
                            <?php $this->load->view('admin/wip/components/v_render_metadata', ['item' => $item, 'category_schemas' => $category_schemas, 'only_badges' => false, 'only_instructions' => true]); ?>

                            <!-- 6. KETERANGAN -->
                            <?php 
                                $pb_oleh = null;
                                $pb_via = null;
                                $pb_note = null;
                                $clean_catatan = [];
                                if (!empty($item->catatan)) {
                                    $lines = explode("\n", $item->catatan);
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
                            <?php if (!empty($final_catatan) || !empty($item->deskprint_nama) || !empty($item->assigned_user) || !empty($item->op_cetak_name) || !empty($item->op_finishing_name) || !empty($pb_oleh)): ?>
                                <div class="mt-1 ps-2 border-start border-3 border-warning py-1 bg-warning bg-opacity-10 rounded-end">
                                    <div class="small fw-bold text-warning-emphasis mb-1" style="font-size: 0.6rem;"><i class="fas fa-sticky-note me-1"></i>KETERANGAN:</div>
                                    <div class="d-flex flex-column gap-1" style="font-size: 0.65rem;">
                                        <?php 
                                            // Get Lokasi Pesan & Lokasi Produksi names
                                            $nospk_parts = explode('-', $item->nospk);
                                            $spk_cabang_id = isset($nospk_parts[0]) ? (int)$nospk_parts[0] : 1;
                                            $lokasi_cetak = $item->lokasi_cetak ?? 1;
                                            
                                            $lokasi_pesan_nama = 'TGM OFFICIAL (PUSAT)';
                                            if ($spk_cabang_id == 3) {
                                                $lokasi_pesan_nama = 'TGM MERCH (CABANG)';
                                            } elseif ($spk_cabang_id > 3) {
                                                $lokasi_pesan_nama = 'CABANG LAIN';
                                            }

                                            $lokasi_produksi_nama = 'TGM OFFICIAL (PUSAT)';
                                            if ($lokasi_cetak == 3) {
                                                $lokasi_produksi_nama = 'TGM MERCH (CABANG)';
                                            } elseif ($lokasi_cetak > 3) {
                                                $lokasi_produksi_nama = 'CABANG LAIN';
                                            }
                                        ?>
                                        <div class="text-muted">Lokasi Pesan: <span class="fw-bold text-dark"><?= htmlspecialchars($lokasi_pesan_nama) ?></span></div>
                                        <div class="text-muted">Lokasi Produksi: <span class="fw-bold text-dark"><?= htmlspecialchars($lokasi_produksi_nama) ?></span></div>
                                        <?php if (!empty($item->deskprint_nama)): ?>
                                            <div class="text-muted" style="font-size: 0.6rem;">Deskprint: <span><?= htmlspecialchars($item->deskprint_nama) ?></span></div>
                                        <?php endif; ?>

                                        <?php if (in_array($st, ['proses', 'finishing'], true)): ?>
                                            <?php if (!empty($item->assigned_user)): ?>
                                                <div class="text-muted">Dikerjakan Oleh Operator: <span class="fw-bold text-dark"><?= htmlspecialchars($item->assigned_user) ?></span></div>
                                            <?php endif; ?>
                                        <?php elseif ($st === 'done' || $st === 'on_pb' || $st === 'on_costumer'): ?>
                                            <?php if (!empty($item->op_cetak_name)): ?>
                                                <div class="text-muted">Dikerjakan Oleh Operator: <span class="fw-bold text-dark"><?= htmlspecialchars($item->op_cetak_name) ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($item->op_finishing_name)): ?>
                                                <div class="text-muted">Finishing oleh: <span class="fw-bold text-dark"><?= htmlspecialchars($item->op_finishing_name) ?></span></div>
                                            <?php endif; ?>
                                            
                                            <?php if ($st === 'on_costumer' && !empty($pb_oleh)): ?>
                                                <div class="text-muted">Diberikan ke konsumen oleh: <span class="fw-bold text-dark"><?= htmlspecialchars($pb_oleh) ?> (PB)</span> melalui: <span class="fw-bold text-dark"><?= htmlspecialchars($pb_via) ?></span></div>
                                                <?php if (!empty($pb_note)): ?>
                                                    <div class="text-muted fst-italic"><i class="fas fa-comment-dots me-1"></i>Catatan PB: <span class="text-dark"><?= htmlspecialchars($pb_note) ?></span></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        // Display routing history (completed steps and their operators)
                                        if (!empty($item->routing_steps)) {
                                            $history_arr = [];
                                            foreach ($item->routing_steps as $step) {
                                                if ($step->status_produksi == '2' && !empty($step->op_name)) {
                                                    $history_arr[] = "<span class='fw-bold text-dark'>" . htmlspecialchars($step->op_name) . "</span> <span class='text-muted fst-italic'>(" . htmlspecialchars($step->routing_name) . ")</span>";
                                                }
                                            }
                                            if (!empty($history_arr)) {
                                                echo '<div class="text-muted mt-1 border-top border-warning-subtle pt-1 pb-1">Riwayat Pengerjaan: <div class="lh-sm mt-1" style="font-size:0.65rem;">' . implode('<br>', $history_arr) . '</div></div>';
                                            }
                                        }
                                        ?>
                                    </div>

                                    <?php if (!empty($final_catatan)): ?>
                                        <div class="text-dark small lh-sm fst-italic mt-1" style="font-size: 0.75rem;">
                                            <i class="fas fa-quote-left text-warning opacity-50 me-1"></i><?= nl2br(htmlspecialchars($final_catatan)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>                        </div>
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <?php
                            $status_text = 'Belum di Proses';
                            $status_class = 'bg-secondary bg-opacity-10 text-secondary border-secondary';
                            
                            if (in_array($st, ['proses', 'finishing'], true)) {
                                $status_text = 'Proses Produksi';
                                $status_class = 'bg-warning bg-opacity-10 text-warning border-warning';
                            } elseif ($st === 'done') {
                                $status_text = 'Selesai Produksi';
                                $status_class = 'bg-success bg-opacity-10 text-success border-success';
                            } elseif ($st === 'on_pb') {
                                $status_text = 'Selesai (Barang di PB)';
                                $status_class = 'bg-success bg-opacity-10 text-success border-success';
                            } elseif ($st === 'on_costumer') {
                                $status_text = 'Di Konsumen';
                                $status_class = 'bg-primary bg-opacity-10 text-primary border-primary';
                            }
                            ?>
                            <span class="badge rounded-pill border <?= $status_class ?>"
                                style="padding: 6px 12px; font-size: 0.65rem; letter-spacing: 0.5px; min-width: 85px; font-weight: 800;">
                                <?= strtoupper($status_text) ?>
                            </span>
                            <?php if (in_array($st, ['proses', 'finishing'], true) && !empty($item->current_step_name)): ?>
                                <span class="text-primary fw-900 text-center"
                                    style="font-size: 0.55rem; max-width: 100%; white-space: normal; line-height: 1.1;"><?= strtoupper($item->current_step_name) ?></span>
                                <?php
                                $locs = [];
                                foreach (($item->routing_steps ?? []) as $step) {
                                    if ($step->status_produksi == '1') {
                                        if (!empty($step->mesin_nama))
                                            $locs[] = '<i class="fas fa-print"></i> ' . $step->mesin_nama;
                                    }
                                }
                                ?>
                                <?php if (!empty($locs)): ?>
                                    <div class="mt-1 d-flex flex-wrap gap-1 justify-content-center">
                                        <?php foreach (array_unique($locs) as $l): ?>
                                            <span class="badge bg-light text-primary border"
                                                style="font-size: 0.5rem; padding: 2px 4px;"><?= $l ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($item->tgl_mulai)): ?>
                                <div class="mt-2 p-1 rounded bg-light border w-100 text-start shadow-sm"
                                    style="font-size: 0.7rem;">
                                    <div class="text-primary fw-bold" style="white-space: nowrap;">
                                        <i class="fas fa-play-circle me-1"></i> Mulai:
                                        <?= date('H:i', strtotime($item->tgl_mulai)) ?> WIB
                                    </div>
                                    <?php if (!empty($item->tgl_selesai) && $item->status_produksi == 'done'): ?>
                                        <div class="text-success fw-bold" style="white-space: nowrap;">
                                            <i class="fas fa-check-circle me-1"></i> Selesai:
                                            <?= date('H:i', strtotime($item->tgl_selesai)) ?> WIB
                                        </div>
                                        <?php
                                        $diff_prod = max(0, strtotime($item->tgl_selesai) - strtotime($item->tgl_mulai));
                                        $diff_prod_m = floor($diff_prod / 60);
                                        $diff_prod_h = floor($diff_prod_m / 60);
                                        $diff_prod_m = $diff_prod_m % 60;
                                        $dur_text = '';
                                        if ($diff_prod_h > 0)
                                            $dur_text .= $diff_prod_h . 'j ';
                                        $dur_text .= $diff_prod_m . 'm';
                                        ?>
                                        <div class="text-muted mt-1 pt-1 border-top" style="font-size: 0.65rem;">
                                            <i class="fas fa-stopwatch me-1"></i> Durasi: <?= $dur_text ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($item->assigned_user) && !in_array($st, ['on_pb', 'on_costumer'])): ?>
                                        <div class="text-primary fw-bold mt-1 pt-1 border-top" style="font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="PIC: <?= htmlspecialchars($item->assigned_user) ?>">
                                            <i class="fas fa-user-check me-1"></i> Oleh: <?= strtoupper(htmlspecialchars($item->assigned_user)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">
                        <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                            <!-- Mulai / Selesai (Start / Finish) -->
                            <?php if ($can_execute): ?>
                                <?php if ($is_waiting_upstream): ?>
                                    <button type="button"
                                        class="btn shadow-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2"
                                        style="background-color: #6c757d; color: white; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px; border: 2px solid transparent; min-width: 120px; cursor: not-allowed;"
                                        disabled title="Menunggu proses <?= $active_step_name ?>">
                                        <i class="fas fa-hourglass-half"></i> <span>MENUNGGU</span>
                                    </button>
                                <?php elseif ($active_step_status === '0' || $st === 'approved' || $st === 'pending' || $st === '0' || $st === ''): ?>
                                    <button type="button"
                                        class="btn shadow-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2 animate__animated animate__pulse animate__infinite"
                                        style="background-color: #2b3a4a; color: white; font-weight: 900; font-size: 0.85rem; letter-spacing: 0.5px; border: 2px solid transparent; min-width: 120px;"
                                        onmouseover="this.style.transform='scale(1.05)'; this.classList.remove('animate__infinite')"
                                        onmouseout="this.style.transform='scale(1)'; this.classList.add('animate__infinite')"
                                        onclick="ProductionApp.advanceStep(<?= $item->produksi_id ?>, 'start', 'Produksi', 0, 0, 0, '<?= $item->satuan_nama ?>', <?= $item->kategori_id ?>)">
                                        <i class="fas fa-play"></i> <span>MULAI</span>
                                    </button>
                                <?php elseif ($active_step_status === '1'): ?>
                                    <button type="button"
                                        class="btn shadow-sm rounded-pill px-3 py-2 d-flex align-items-center justify-content-center gap-2"
                                        style="background-color: #dc3545; color: white; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px; border: 2px solid transparent;"
                                        title="Batal Proses"
                                        onclick="ProductionApp.advanceStep(<?= $item->produksi_id ?>, 'cancel', 'Produksi', 0, 0, 0, '<?= $item->satuan_nama ?>', <?= $item->kategori_id ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button type="button"
                                        class="btn shadow-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2 animate__animated animate__pulse animate__infinite"
                                        style="background-color: #28a745; color: white; font-weight: 900; font-size: 0.85rem; letter-spacing: 0.5px; border: 2px solid transparent; min-width: 120px;"
                                        onmouseover="this.style.transform='scale(1.05)'; this.classList.remove('animate__infinite')"
                                        onmouseout="this.style.transform='scale(1)'; this.classList.add('animate__infinite')"
                                        onclick="ProductionApp.finishItemIndividual(<?= $item->produksi_id ?>)">
                                        <i class="fas fa-check-double"></i> <span>SELESAI</span>
                                    </button>
                                <?php elseif ($st === 'on_costumer'): ?>
                                    <button type="button"
                                        class="btn shadow-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2"
                                        style="background-color: #28a745; color: white; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px; border: 2px solid transparent; min-width: 120px; cursor: default;"
                                        disabled title="Telah Diserahkan ke Konsumen">
                                        <i class="fas fa-check-circle"></i> <span>SELESAI</span>
                                    </button>
                                <?php elseif ($st === 'on_pb'): ?>
                                    <button type="button"
                                        class="btn shadow-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2"
                                        style="background-color: #198754; color: white; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px; border: 2px solid transparent; min-width: 120px; cursor: default;"
                                        disabled title="Barang Sedang di Pengambilan Barang (PB)">
                                        <i class="fas fa-box"></i> <span>DI PB</span>
                                    </button>
                                <?php elseif ($st === 'done' && !$can_execute): ?>
                                    <button type="button"
                                        class="btn shadow-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2"
                                        style="background-color: #0d6efd; color: white; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px; border: 2px solid transparent; min-width: 120px; cursor: default;"
                                        disabled title="Produksi Selesai, Menunggu PB">
                                        <i class="fas fa-check"></i> <span>SLS PRODUKSI</span>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>



                            <!-- KE PB button -->
                            <?php if ($st === 'done' && $can_execute): ?>
                                <button type="button"
                                    class="btn shadow-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2 animate__animated animate__pulse animate__infinite"
                                    style="background-color: #00c0ef; color: white; font-weight: 900; font-size: 0.85rem; letter-spacing: 0.5px; border: 2px solid transparent; min-width: 120px;"
                                    onmouseover="this.style.transform='scale(1.05)'; this.classList.remove('animate__infinite')"
                                    onmouseout="this.style.transform='scale(1)'; this.classList.add('animate__infinite')"
                                    onclick="moveToPB(<?= $item->produksi_id ?>)">
                                    <i class="fas fa-paper-plane"></i> <span>KIRIM PB</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    if (typeof window.initBomModal === 'undefined') {
        window.initBomModal = true;

        // Add modal HTML to body if it doesn't exist
        if (!document.getElementById('bomMaterialsModal')) {
            const modalHtml = `
        <div class="modal fade" id="bomMaterialsModal" tabindex="-1" aria-labelledby="bomMaterialsModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
              <div class="modal-header bg-light border-bottom border-primary border-3">
                <h5 class="modal-title fw-bold text-dark mb-0"><i class="fas fa-box-open me-2 text-primary"></i>Kebutuhan Bahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-3">
                <div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center" style="font-size: 0.85rem; border-left: 4px solid #ffc107;">
                    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                    <div>Silahkan Cek Ketersediaan Stok Bahan Berikut sebelum melakukan Produksi</div>
                </div>
                <div class="fw-bold text-dark mb-3 border-bottom pb-2" style="font-size: 0.95rem;" id="bomModalProductName"></div>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" id="bomModalListContainer"></ul>
              </div>
            </div>
          </div>
        </div>`;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // Use jQuery for event delegation
        $(document).on('click', '.btn-show-bom-modal', function () {
            const productName = $(this).data('product');
            const bomData = $(this).data('bom');

            $('#bomModalProductName').text(productName);
            const listContainer = $('#bomModalListContainer');
            listContainer.empty();

            if (bomData && bomData.length > 0) {
                bomData.forEach(item => {
                    let calcDetail = '';
                    if (item.base_qty !== undefined && item.order_qty !== undefined) {
                        calcDetail = `<div class="text-muted mt-1" style="font-size: 0.8rem; margin-left: 0.5rem;"><i class="fas fa-calculator me-1 text-secondary"></i> Kebutuhan: (1 Pcs = ${item.base_qty} ${item.unit}) &times; (${item.order_qty} Pesanan)</div>`;
                    }
                    
                    listContainer.append(`
                    <li class="d-flex flex-column border-bottom border-secondary border-opacity-10 pb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-dark fw-semibold text-wrap pe-2" style="font-size: 0.85rem; line-height: 1.3;">&bull; ${item.name}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold border border-primary border-opacity-25 shadow-sm text-nowrap" style="font-size: 0.85rem;">Total: ${item.qty} ${item.unit}</span>
                        </div>
                        ${calcDetail}
                    </li>
                `);
                });
            }

            const modal = new bootstrap.Modal(document.getElementById('bomMaterialsModal'));
            modal.show();
        });
    }
</script>

<style>
    /* Premium overrides for tableItemsProduksi matching transactions table style */
    .premium-table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
        border: 1px solid #eef2f5 !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }

    .premium-table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        font-size: 0.7rem !important;
        letter-spacing: 0.5px !important;
        border-bottom: 2px solid #e2e8f0 !important;
        border-top: none !important;
        padding: 14px 16px !important;
        vertical-align: middle !important;
    }

    .premium-table tbody tr {
        transition: all 0.15s ease !important;
    }

    .premium-table tbody tr:hover {
        background-color: #f8fafc !important;
    }

    .premium-table tbody td {
        padding: 14px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        border-right: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
        color: #334155 !important;
    }

    .premium-table tbody td:last-child {
        border-right: none !important;
    }

    /* Style for SPK ID */
    .spk-id-text {
        font-size: 1.15rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.5px !important;
    }

    /* Style for premium badges */
    .badge-hari-ini {
        background-color: #00a2b9 !important;
        color: #ffffff !important;
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        width: fit-content !important;
        gap: 4px !important;
        letter-spacing: 0.5px !important;
    }

    .badge-kemarin {
        background-color: #64748b !important;
        color: #ffffff !important;
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        width: fit-content !important;
        gap: 4px !important;
        letter-spacing: 0.5px !important;
    }

    .badge-relative-time {
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }

    .badge-relative-time.bg-success {
        background-color: #28a745 !important;
        color: #ffffff !important;
    }

    .badge-relative-time.bg-info {
        background-color: #00a2b9 !important;
        color: #ffffff !important;
    }

    /* Action buttons matching screenshot style */
    .btn-action-premium {
        width: 32px !important;
        height: 32px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 4px !important;
        border: none !important;
        transition: all 0.2s ease !important;
        padding: 0 !important;
        margin: 0 1px !important;
        text-decoration: none !important;
        cursor: pointer !important;
    }

    .btn-action-premium i {
        font-size: 0.85rem !important;
    }

    .btn-action-premium:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        opacity: 0.95 !important;
    }
</style>