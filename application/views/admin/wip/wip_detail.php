<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- Detail Header -->
        <div class="card border-0 shadow-sm mb-4 bg-primary text-white overflow-hidden position-relative">
             <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="fas fa-clipboard-list fa-5x"></i>
             </div>
             <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Detail Produksi</h4>
                        <h6 class="opacity-75 mb-0">SPK: <?= $produksi->nospk ?? 'N/A' ?></h6>
                    </div>
                     <div class="text-end">
                        <a href="<?= site_url('wip') ?>" class="btn btn-light btn-sm text-primary fw-bold">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke WIP
                        </a>
                     </div>
                </div>
             </div>
        </div>

        <?php
        // Cek apakah SPK ini punya finishing
        $spk_has_finishing = false;
        $spk_finishing_name = 'Finishing';
        if (isset($produksi->finishing_id) && !empty($produksi->finishing_id)) {
            $spk_has_finishing = true;
            // Coba ambil nama finishing
            $f_name_q = $this->db->get_where('tbl_product', ['product_id' => $produksi->finishing_id])->row();
            if ($f_name_q) $spk_finishing_name = $f_name_q->product_nama;
        }
        // Cek juga dari detail_produksi apakah ada baris is_finishing = 1
        $detail_has_finishing = false;
        if (!$spk_has_finishing) {
            foreach ($detail_produksi as $dp_check) {
                if (isset($dp_check->is_finishing) && !empty($dp_check->is_finishing) && $dp_check->is_finishing == 1) {
                    $detail_has_finishing = true;
                    break;
                }
            }
        }
        $need_finishing = $spk_has_finishing || $detail_has_finishing;
        ?>

        <?php if ($need_finishing): ?>
        <div class="alert alert-warning border-start border-warning border-4 shadow-sm mb-4 py-3 px-4 d-flex align-items-center gap-3" role="alert">
            <i class="fas fa-exclamation-triangle fa-2x text-warning flex-shrink-0"></i>
            <div>
                <strong class="d-block fs-6">SPK Ini Memiliki Proses Finishing!</strong>
                <span class="text-muted small">
                    Setelah proses <strong>Cetak/Print selesai</strong>, SPK ini akan otomatis diarahkan ke antrian
                    <strong class="text-warning">Finishing: <?= htmlspecialchars($spk_finishing_name) ?></strong>
                    &mdash; <em>TIDAK langsung ke PB</em>.
                    Pastikan proses finishing diselesaikan terlebih dahulu sebelum barang diserahkan ke PB.
                </span>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4 py-3 px-4 d-flex align-items-center gap-3" role="alert">
            <i class="fas fa-info-circle fa-2x text-info flex-shrink-0"></i>
            <div>
                <strong class="d-block fs-6">SPK Tanpa Finishing</strong>
                <span class="text-muted small">
                    SPK ini <strong>tidak memiliki proses finishing</strong>.
                    Setelah semua proses produksi selesai, barang akan langsung masuk ke antrian <strong class="text-info">PB (Pengambilan Barang)</strong>.
                </span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stats Overview -->
        <div class="row g-3 mb-4">
             <div class="col-md-3">
                <div class="hr-stat-card card-primary p-3 rounded-3 shadow-sm text-center h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-2 opacity-10">
                        <i class="fas fa-box fa-3x"></i>
                    </div>
                     <h6 class="text-uppercase text-muted opacity-75 small fw-bold mb-1">Produk</h6>
                    <h5 class="fw-bold mb-0 text-truncate text-dark" title="<?= $produksi->product_nama ?? 'N/A' ?>"><?= $produksi->product_nama ?? 'N/A' ?></h5>
                </div>
            </div>
            <div class="col-md-3">
                 <div class="hr-stat-card card-success p-3 rounded-3 shadow-sm text-center h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-2 opacity-10">
                        <i class="fas fa-cubes fa-3x"></i>
                    </div>
                     <h6 class="text-uppercase text-muted opacity-75 small fw-bold mb-1">Qty Pemesanan</h6>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($produksi->qty ?? 0) ?></h3>
                    <small class="text-muted"><?= $produksi->satuan_nama ?? 'Unit' ?></small>
                </div>
            </div>
             <div class="col-md-3">
                <div class="hr-stat-card card-info p-3 rounded-3 shadow-sm text-center h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-2 opacity-10">
                        <i class="fas fa-print fa-3x"></i>
                    </div>
                     <h6 class="text-uppercase text-muted opacity-75 small fw-bold mb-1">Status Approve</h6>
                    <div class="mt-1">
                        <?php 
                        $approve_status = isset($produksi->approve) ? (int)$produksi->approve : 0;
                        if ($approve_status == 1) {
                            echo '<span class="badge bg-success">Cetak Semua</span>';
                        } elseif ($approve_status == 2) {
                            echo '<span class="badge bg-warning text-dark">Cetak Sampel</span>';
                        } else {
                            echo '<span class="badge bg-secondary">Belum Approve</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                <div class="hr-stat-card card-warning p-3 rounded-3 shadow-sm text-center h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-2 opacity-10">
                        <i class="fas fa-tasks fa-3x"></i>
                    </div>
                     <h6 class="text-uppercase text-muted opacity-75 small fw-bold mb-1">Total Routing</h6>
                    <h3 class="fw-bold mb-0 text-dark"><?= count($detail_produksi) ?></h3>
                </div>
            </div>
        </div>

        <!-- Production Details Table -->
        <div class="card border-0 shadow-sm">
             <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                 <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2"></i>Detail Routing Produksi</h6>
                    <?php if (!empty($produksi->detail_product)): ?>
                        <small class="text-muted"><i class="fas fa-tag me-1"></i>Detail: <?= $produksi->detail_product ?></small>
                    <?php endif; ?>
                 </div>
                 <span class="badge bg-light text-dark border">
                    <i class="fas fa-info-circle me-1"></i><?= count($detail_produksi) ?> Steps
                 </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tableDetai" class="table table-hover align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="12%">Routing</th>
                                <th width="15%">Detail Produk</th>
                                <th width="18%">Operator / Mesin / Lokasi</th>
                                <th width="8%" class="text-center">Qty Pesan</th>
                                <th width="8%" class="text-center">Qty BOM</th>
                                <th width="8%" class="text-center">Target</th>
                                <th width="8%" class="text-center">Sukses</th>
                                <th width="8%" class="text-center">Gagal</th>
                                <th width="10%">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($detail_produksi)) : ?>
                                <?php 
                                $no = 1; 
                                $user_level = $this->session->userdata('level');
                                $is_admin_or_owner = in_array($user_level, [1, 17]); // Level 1 = Admin, Level 17 = Owner
                                $previous_routing_completed = true; // Track if previous routing is completed
                                
                                foreach ($detail_produksi as $d) : 
                                    // Check if previous routing step is completed
                                    $current_routing_urutan = $d->routing_urutan ?? 0;
                                    $can_show_routing = $previous_routing_completed;
                                    
                                    // If this is not the first routing, check if previous step is completed
                                    if ($current_routing_urutan > 1) {
                                        $previous_routing_urutan = $current_routing_urutan - 1;
                                        $previous_routing = array_filter($detail_produksi, function($item) use ($previous_routing_urutan) {
                                            return ($item->routing_urutan ?? 0) == $previous_routing_urutan;
                                        });
                                        
                                        if (!empty($previous_routing)) {
                                            $previous_routing_item = reset($previous_routing);
                                            $previous_routing_completed = ($previous_routing_item->status_produksi ?? '0') >= '2'; // Status 2 or higher means completed
                                        }
                                    }
                                    
                                    // Admin and Owner can see all routing steps regardless of completion status
                                    if ($is_admin_or_owner) {
                                        $can_show_routing = true;
                                    }
                                    
                                    // Only show if previous routing is completed or this is the first routing or user is admin/owner
                                    if ($can_show_routing):
                                ?>
                                    <tr class="<?= (!$can_show_routing && !$is_admin_or_owner) ? 'bg-light opacity-75' : '' ?>">
                                        <td class="text-center fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="badge bg-primary-subtle text-primary rounded-circle p-2 me-2">
                                                     <span class="fw-bold" style="font-size: 0.8rem;"><?= $d->routing_urutan ?? '#' ?></span>
                                                </div>
                                                <span class="fw-bold text-dark"><?= $d->routing_nama ?? 'Routing' ?></span>
                                            </div>
                                            <div class="small text-muted ps-1 border-start ms-3">
                                                 <div class="ms-2"><i class="fas fa-play fa-xs me-1"></i> <?= $d->waktu_mulai ? date('d/m H:i', strtotime($d->waktu_mulai)) : '-' ?></div>
                                                 <div class="ms-2"><i class="fas fa-check fa-xs me-1"></i> <?= $d->waktu_selesai ? date('d/m H:i', strtotime($d->waktu_selesai)) : '-' ?></div>
                                            </div>
                                             <?php if (!$can_show_routing && !$is_admin_or_owner): ?>
                                                <span class="badge bg-warning text-dark mt-1"><i class="fas fa-lock me-1"></i>Locked</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="small fw-semibold text-wrap"><?= $produksi->detail_product ?? '-' ?></span>
                                            </div>
                                            <span class="badge bg-info text-white rounded-pill px-3 py-1 fw-bold border-0 shadow-sm" style="font-size: 0.7rem;">
                                                <i class="fas fa-layer-group me-1"></i> <?= $produksi->qty ?> <?= $produksi->satuan_nama ?>
                                            </span>
                                            
                                            <?php if ($produksi->kategori_id == 2 && !empty($produksi->jenisprint_id)): ?>
                                                <?php
                                                $machineLabel = 'LAINNYA';
                                                if ($produksi->jenisprint_id == 2) $machineLabel = 'OUTDOOR';
                                                elseif ($produksi->jenisprint_id == 3) $machineLabel = 'INDOOR SOLVENT';
                                                elseif ($produksi->jenisprint_id == 4) $machineLabel = 'INDOOR UV';
                                                ?>
                                                <span class="badge bg-dark text-white rounded-pill px-3 py-1 fw-bold border-0 shadow-sm" style="font-size: 0.7rem;">
                                                    <i class="fas fa-print me-1"></i> <?= $machineLabel ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($produksi->sisi) && $produksi->kategori_id != 9): ?>
                                                <span class="badge bg-secondary text-white rounded-pill px-3 py-1 fw-bold border-0 shadow-sm" style="font-size: 0.7rem;">
                                                    <i class="fas fa-arrows-alt-h me-1"></i> <?= $produksi->sisi ?> Sisi
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <i class="fas fa-user text-muted me-1"></i> <span class="small fw-semibold"><?= $d->operator_nama ?? '-' ?></span>
                                            </div>
                                            <div class="mb-1">
                                                <i class="fas fa-cog text-muted me-1"></i> <span class="small"><?= $d->mesin_nama ?? '-' ?></span>
                                            </div>
                                             <div>
                                                <span class="badge bg-light text-secondary border"><?= $d->nama_perusahaan ?? '-' ?></span>
                                             </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark"><?= number_format($produksi->qty ?? 0) ?></div>
                                            <small class="text-muted" style="font-size:0.7rem"><?= $produksi->satuan_nama ?? 'Unit' ?></small>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark"><?= number_format($d->quantity ?? 0) ?></div> <!-- Qty BOM/Consumption -->
                                            <!-- <small class="text-muted" style="font-size:0.7rem"><?= $d->bahan_nama ?? '-' ?></small> -->
                                        </td>
                                        <td class="text-center">
                                            <?php $jumlah = ($d->quantity ?? 0) * ($d->qty_input ?? 0); // Logic consumption total? Adjust as needed ?>
                                             <div class="fw-bold text-primary"><?= number_format($d->qty_input ?? $produksi->qty ?? 0) ?></div> <!-- Target / Input -->
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold text-success"><?= number_format($d->qty_output ?? 0) ?></div>
                                            <?php if($d->panjang_output || $d->lebar_output): ?>
                                            <small class="text-muted d-block" style="font-size:0.65rem">
                                                <?php if (isset($produksi->satuan_id) && $produksi->satuan_id == 11): ?>
                                                    <?= (float)$d->panjang_output ?> M <?= !empty($d->lebar_output) ? "(L: ".(float)$d->lebar_output."m)" : '' ?>
                                                <?php elseif (!empty($d->panjang_output) && empty($d->lebar_output)): ?>
                                                    <?= (float)$d->panjang_output ?> M
                                                <?php else: ?>
                                                    <?= (float)$d->panjang_output ?>x<?= (float)$d->lebar_output ?>m
                                                <?php endif; ?>
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold text-danger"><?= number_format($d->qty_missing ?? 0) ?></div>
                                             <?php if($d->alasan_missing): ?>
                                            <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" title="<?= $d->alasan_missing ?>"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusKode = isset($d->status_produksi) ? (string)$d->status_produksi : '0';
                                            if ($statusKode === '4') {
                                                echo '<span class="badge bg-dark w-100 py-2"><i class="fas fa-user-check me-1"></i>Diserahkan</span>';
                                            } elseif ($statusKode === '3') {
                                                echo '<span class="badge bg-primary w-100 py-2"><i class="fas fa-box-seam me-1"></i>Di PB</span>';
                                            } elseif ($statusKode === '2') {
                                                echo '<span class="badge bg-success w-100 py-2"><i class="fas fa-check-circle me-1"></i>Selesai</span>';
                                            } elseif ($statusKode === '1') {
                                                echo '<span class="badge bg-warning text-dark w-100 py-2"><i class="fas fa-cog fa-spin me-1"></i>Proses</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary w-100 py-2"><i class="fas fa-clock me-1"></i>Pending</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($statusKode === '0'): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-success" 
                                                            onclick="startProcess(<?= $d->detail_produksi_id ?? 0 ?>, <?= $d->routing_urutan ?? 0 ?>)" 
                                                            data-bs-toggle="tooltip" 
                                                            title="Mulai"
                                                            <?= (!$can_show_routing && !$is_admin_or_owner) ? 'disabled' : '' ?>>
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                <?php elseif ($statusKode === '1'): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-warning" 
                                                            onclick="completeProcess(<?= $d->detail_produksi_id ?? 0 ?>, <?= $d->routing_urutan ?? 0 ?>)" 
                                                            data-bs-toggle="tooltip" 
                                                            title="Selesaikan"
                                                            <?= (!$can_show_routing && !$is_admin_or_owner) ? 'disabled' : '' ?>>
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button type="button" 
                                                        class="btn btn-outline-secondary" 
                                                        onclick="processProduction(<?= $d->detail_produksi_id ?? 0 ?>, '<?= $statusKode ?>', <?= $d->routing_urutan ?? 0 ?>)" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Edit Proses"
                                                        <?= (!$can_show_routing && !$is_admin_or_owner) ? 'disabled' : '' ?>>
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                
                                                <?php if ($statusKode >= '2'): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-dark" 
                                                        onclick="printProductionTag(<?= $d->detail_produksi_id ?? 0 ?>, '<?= $d->routing_urutan ?? 0 ?>', '<?= htmlspecialchars($d->product_nama ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($d->operator_nama ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($d->mesin_nama ?? '', ENT_QUOTES) ?>', '<?= $d->qty_output ?? 0 ?>', '<?= $d->panjang_output ?? 0 ?>', '<?= $d->lebar_output ?? 0 ?>')" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Cetak Tag">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    endif; // Close can_show_routing check
                                endforeach; 
                                ?>

                                <?php 
                                // Hidden routing logic for non-admin
                                $hidden_routing_count = 0;
                                if (!$is_admin_or_owner) {
                                    foreach ($detail_produksi as $d) {
                                        $curr = $d->routing_urutan ?? 0;
                                        if ($curr > 1) {
                                            $prev_order = $curr - 1;
                                            $prev_item = array_filter($detail_produksi, function($i) use ($prev_order) {
                                                return ($i->routing_urutan ?? 0) == $prev_order;
                                            });
                                            if(!empty($prev_item)) {
                                                $p_item = reset($prev_item);
                                                if (($p_item->status_produksi ?? '0') < '2') {
                                                    $hidden_routing_count++;
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($hidden_routing_count > 0): ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-3 bg-light">
                                            <div class="text-muted small">
                                                <i class="fas fa-lock me-1"></i> <strong><?= $hidden_routing_count ?> langkah routing</strong> terkunci sampai langkah sebelumnya selesai.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            <?php else : ?>
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <div class="text-muted opacity-50">
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <h5>Belum ada detail produksi</h5>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chatter History / Log Produksi (ERP Level) -->
        <div class="card border-0 shadow-sm mt-4 mb-4">
            <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Riwayat Aktivitas & Perubahan Status</h6>
            </div>
            <div class="card-body bg-light">
                <div id="produksi_chatter_container">
                    <div class="text-center p-3 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Memuat riwayat...</div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Stock Update Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-check-circle me-2"></i>Selesaikan Proses & Update Stok
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stockForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="detail_produksi_id" name="detail_produksi_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Output Sukses</label>
                            <input type="number" class="form-control" id="qty_output" name="qty_output" required placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Output Gagal (Missing)</label>
                            <input type="number" class="form-control" id="qty_missing" name="qty_missing" value="0" placeholder="0">
                        </div>

                         <div class="col-12"><hr class="my-2 opacity-10"></div>

                         <div class="col-md-6">
                             <label class="form-label small fw-bold text-uppercase text-muted">Mesin Digunakan <span class="text-danger">*</span></label>
                             <select class="form-select" id="mesin_id_stock" name="mesin_id" required>
                                <option value="">-- Pilih Mesin --</option>
                                <option value="0">-- Manual --</option>
                                <?php foreach ($mesin as $m): ?>
                                <option value="<?= $m->mesin_id ?>"><?= $m->mesin_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                         </div>
                          <div class="col-md-6">
                             <label class="form-label small fw-bold text-uppercase text-muted">Lokasi Cetak <span class="text-danger">*</span></label>
                             <select class="form-select" id="lokasi_cetak_stock" name="lokasi_proses" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php $first = true; foreach ($perusahaan as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>" <?= $first ? 'selected' : '' ?>><?= $p->nama_perusahaan ?></option>
                                <?php $first = false; endforeach; ?>
                            </select>
                         </div>
                         
                         <div class="col-12">
                             <label class="form-label small fw-bold text-uppercase text-muted">Material</label>
                             <select class="form-select" id="material_id_stock" name="material_id">
                                <option value="0">Default (BOM)</option>
                                <option value="">-- Pilih Material (Override) --</option>
                                <?php foreach ($bahan_baku as $bb): ?>
                                <option value="<?= $bb->material_id ?>"><?= $bb->varian ?? $bb->material_id ?></option>
                                <?php endforeach; ?>
                            </select>
                             <div class="form-text small" id="currentMaterialStock">Default: Loading...</div>
                         </div>

                        <!-- Dimensions Row (Hidden by default) -->
                        <div class="col-12" id="dimensionsRowStock" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                     <label class="form-label small fw-bold text-uppercase text-muted">Panjang Output (m)</label>
                                    <input type="number" step="0.01" class="form-control" id="panjang_output" name="panjang_output">
                                </div>
                                 <div class="col-md-6">
                                     <label class="form-label small fw-bold text-uppercase text-muted">Lebar Output (m)</label>
                                    <input type="number" step="0.01" class="form-control" id="lebar_output" name="lebar_output">
                                </div>
                            </div>
                        </div>

                         <div class="col-12">
                             <label class="form-label small fw-bold text-uppercase text-muted">Catatan / Alasan Gagal</label>
                            <textarea class="form-control" id="alasan_missing" name="alasan_missing" rows="2"></textarea>
                         </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Process Modal -->
<div class="modal fade" id="productionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2"></i>Update Status Produksi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
             <form id="productionForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="production_detail_id" name="detail_produksi_id">
                    <input type="hidden" id="production_status" name="current_status">
                    
                     <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Status Baru <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_produksi" name="status_produksi" required>
                                <option value="0">Belum Di Proses</option>
                                <option value="1">Sedang Di Proses</option>
                                <option value="2">Selesai Di Proses</option>
                                <option value="3">Barang Di PB</option>
                             </select>
                        </div>
                         <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Mesin <span class="text-danger">*</span></label>
                             <select class="form-select" id="mesin_id" name="mesin_id" required>
                                <option value="0">Manual</option>
                                 <?php foreach ($mesin as $m): ?>
                                 <option value="<?= $m->mesin_id ?>"><?= $m->mesin_nama ?></option>
                                 <?php endforeach; ?>
                             </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Lokasi Proses <span class="text-danger">*</span></label>
                            <select class="form-select" id="lokasi_proses" name="lokasi_proses" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php foreach ($perusahaan as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>"><?= $p->nama_perusahaan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                         <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Input Qty (Target)</label>
                            <input type="number" class="form-control" id="qty_input" name="qty_input" readonly style="background-color: #e9ecef;">
                        </div>

                         <div class="col-6"><hr></div><div class="col-6"><hr></div>

                          <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Output Sukses</label>
                            <input type="number" class="form-control" id="qty_output_prod" name="qty_output">
                        </div>
                         <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Output Gagal</label>
                            <input type="number" class="form-control" id="qty_missing_prod" name="qty_missing">
                        </div>

                        <!-- Dimensions (Hidden by default) -->
                        <div class="col-12" id="dimensionsRow" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Panjang (m)</label>
                                    <input type="number" step="0.01" class="form-control" id="panjang_output_prod" name="panjang_output">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Lebar (m)</label>
                                    <input type="number" step="0.01" class="form-control" id="lebar_output_prod" name="lebar_output">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                             <label class="form-label small fw-bold text-uppercase text-muted">Catatan</label>
                            <textarea class="form-control" id="alasan_missing_prod" name="alasan_missing" rows="2"></textarea>
                         </div>
                     </div>
                </div>
                 <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
             </form>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // DataTable
    if ($.fn.DataTable) {
        $('#tableDetai').DataTable({
            "language": {
                 "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "pageLength": 25,
            "order": [[1, "asc"]],
            "responsive": true,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Form Submissions
    $('#stockForm').on('submit', function(e) {
        e.preventDefault();
        submitStockUpdate();
    });

    $('#productionForm').on('submit', function(e) {
        e.preventDefault();
        submitProductionProcess();
    });
});

function startProcess(detailId, routingUrutan) {
    Swal.fire({
        title: 'Mulai Proses?',
        text: `Mulai langkah ke-${routingUrutan}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Mulai!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= site_url("wip/start_process") ?>', {
                detail_produksi_id: detailId,
                routing_urutan: routingUrutan
            }, function(response) {
                if(response.success) {
                    Swal.fire('Berhasil!', 'Proses dimulai.', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            });
        }
    });
}

function completeProcess(detailId, routingUrutan) {
    // ====================================================================
    // [FIX] NOTIFIKASI: Beritahu operator ke mana SPK ini akan diarahkan
    // setelah proses ini selesai — ke Finishing atau langsung ke PB.
    // ====================================================================
    const needsFinishing = <?= $need_finishing ? 'true' : 'false' ?>;
    const finishingName  = '<?= htmlspecialchars($spk_finishing_name, ENT_QUOTES) ?>';

    let confirmTitle, confirmText, confirmIcon, confirmColor;
    if (needsFinishing) {
        confirmTitle = '⚠️ Selesaikan Proses Cetak?';
        confirmIcon  = 'warning';
        confirmColor = '#f59e0b';
        confirmText  = `Setelah proses ini selesai, SPK akan otomatis diarahkan ke antrian:\n\n` +
                       `🔧 FINISHING: ${finishingName}\n\n` +
                       `Pastikan hasil cetak sudah OK sebelum melanjutkan.`;
    } else {
        confirmTitle = '✅ Selesaikan & Kirim ke PB?';
        confirmIcon  = 'info';
        confirmColor = '#2563eb';
        confirmText  = 'SPK ini tidak memiliki finishing. Setelah disimpan, barang akan langsung masuk ke antrian PB (Pengambilan Barang).';
    }

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: confirmIcon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: needsFinishing ? '✔ Cetak Selesai → Ke Finishing' : '✔ Selesai → Kirim ke PB',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Buka modal input qty output setelah konfirmasi
            $('#stockForm')[0].reset();
            $('#detail_produksi_id').val(detailId);
            loadStockProductionData(detailId);
            loadExistingMachineData(detailId);
            let qtyOrder = <?= $produksi->qty ?? 0 ?>;
            if (qtyOrder > 0) $('#qty_output').val(qtyOrder);
            $('#dimensionsRowStock').hide();
            $('#stockModal').modal('show');
        }
    });
}

function processProduction(detailId, currentStatus, routingUrutan) {
    $('#productionForm')[0].reset();
    $('#production_detail_id').val(detailId);
    $('#production_status').val(currentStatus);
    
    // Logic next status
    let nextSt = '1';
    if(currentStatus == '0') nextSt = '1';
    else if(currentStatus == '1') nextSt = '2';
    else if(currentStatus == '2') nextSt = '3';
    else if(currentStatus == '3') nextSt = '3';
    $('#status_produksi').val(nextSt);

    loadProductionData(detailId);
    $('#productionModal').modal('show');
}

// Helpers for data loading
function loadStockProductionData(detailId) {
    $.post('<?= site_url("wip/get_production_data") ?>', {detail_produksi_id: detailId}, function(res) {
        if(res.success && res.data) {
            let d = res.data;
            if(d.qty_output > 0) $('#qty_output').val(d.qty_output);
            $('#qty_missing').val(d.qty_missing || 0);
            
            if(parseFloat(d.panjang_output) > 0 || parseFloat(d.lebar_output) > 0) {
                $('#dimensionsRowStock').show();
                $('#panjang_output').val(d.panjang_output);
                $('#lebar_output').val(d.lebar_output);
            }
        }
    }, 'json');
}

function loadExistingMachineData(detailId) {
    $.post('<?= site_url("wip/get_production_data") ?>', {detail_produksi_id: detailId}, function(res) {
        if(res.success && res.data) {
            let d = res.data;
            if(d.mesin_id) $('#mesin_id_stock').val(d.mesin_id);
            else loadDefaultMachineFromRouting(detailId); // Fallback
            
            if(d.material_id) {
                $('#material_id_stock').val(d.material_id);
            } else {
                loadDefaultMaterialFromBOM(detailId); // Fallback
            }
            
            if(d.lokasi_proses) $('#lokasi_cetak_stock').val(d.lokasi_proses);
            else {
                 $('#lokasi_cetak_stock').val($('#lokasi_cetak_stock option:eq(1)').val()); // pick first available
            }
            
            $('#currentMaterialStock').text(d.bahan_nama || 'Default');
        } else {
            loadDefaultMachineFromRouting(detailId);
            loadDefaultMaterialFromBOM(detailId);
        }
    }, 'json');
}

function loadDefaultMachineFromRouting(detailId) {
     $.post('<?= site_url("wip/get_routing_data") ?>', {detail_produksi_id: detailId}, function(res) {
        if(res.success && res.data && res.data.mesin_id) {
            $('#mesin_id_stock').val(res.data.mesin_id);
        }
    }, 'json');
}

function loadDefaultMaterialFromBOM(detailId) {
    $.post('<?= site_url("wip/get_bom_data") ?>', {detail_produksi_id: detailId}, function(res) {
        if(res.success && res.data && res.data.material_id) {
            $('#material_id_stock').val(res.data.material_id);
            $('#currentMaterialStock').text(res.data.material_name);
        } else {
            $('#material_id_stock').val('0');
            $('#currentMaterialStock').text('BOM Default');
        }
    }, 'json');
}


function loadProductionData(detailId) {
    $.post('<?= site_url("wip/get_production_data") ?>', {detail_produksi_id: detailId}, function(res) {
        if(res.success && res.data) {
            let d = res.data;
            $('#qty_input').val(d.qty_input || '');
            if(d.qty_output > 0) $('#qty_output_prod').val(d.qty_output);
            $('#qty_missing_prod').val(d.qty_missing || 0);
            
            if(parseFloat(d.panjang_output) > 0 || parseFloat(d.lebar_output) > 0) {
                $('#dimensionsRow').show();
                $('#panjang_output_prod').val(d.panjang_output);
                $('#lebar_output_prod').val(d.lebar_output);
            } else {
                $('#dimensionsRow').hide();
            }

            if(d.mesin_id) $('#mesin_id').val(d.mesin_id);
            if(d.lokasi_proses) $('#lokasi_proses').val(d.lokasi_proses);
            $('#alasan_missing_prod').val(d.alasan_missing || '');
        }
    }, 'json');
}


function submitStockUpdate(supervisorPin = '') {
    Swal.fire({
        title: 'Simpan & Selesaikan?',
        text: 'Stok akan diupdate berdasarkan data ini.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Simpan!'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = $('#stockForm').serialize();
            if (supervisorPin) {
                formData += '&supervisor_pin=' + encodeURIComponent(supervisorPin);
            }
            $.ajax({
                url: '<?= site_url("wip/complete_process") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Berhasil!', 'Data tersimpan.', 'success').then(() => {
                            $('#stockModal').modal('hide');
                            location.reload();
                        });
                    } else if (res.require_pin_override) {
                        Swal.fire({
                            title: 'Otorisasi Dibutuhkan',
                            text: res.message,
                            icon: 'warning',
                            input: 'password',
                            inputPlaceholder: 'Masukkan PIN Supervisor/Otorisasi',
                            showCancelButton: true,
                            confirmButtonText: 'Bypass & Lanjut',
                            cancelButtonText: 'Batal',
                            preConfirm: (pin) => {
                                if (!pin) {
                                    Swal.showValidationMessage('PIN wajib diisi!');
                                    return false;
                                }
                                return pin;
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                submitStockUpdate(result.value);
                            }
                        });
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                }
            });
        }
    });
}

function submitProductionProcess() {
    Swal.fire({
        title: 'Update Status?',
        text: 'Simpan perubahan status produksi?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Update!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= site_url("wip/update_production_process") ?>',
                type: 'POST',
                data: $('#productionForm').serialize(),
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Berhasil!', 'Status diupdate.', 'success').then(() => {
                            $('#productionModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                }
            });
        }
    });
}

function printProductionTag(detailId, routingUrutan, productNama, operatorNama, mesinNama, qtyOutput, panjangOutput, lebarOutput) {
     const detailProduct = '<?= htmlspecialchars($produksi->detail_product ?? "", ENT_QUOTES) ?>';
     let printContent = `
        <html><head><title>Tag Produksi</title>
        <style>
             @media print { @page { size: 80mm 200mm; margin: 0; } }
             body { font-family: monospace; font-size: 12px; width: 300px; margin: 0 auto; padding: 10px; }
             .text-center { text-align: center; }
             .bold { font-weight: bold; }
             .mb-1 { margin-bottom: 5px; }
             .border-bottom { border-bottom: 1px dashed black; padding-bottom: 5px; margin-bottom: 5px; }
             .flex { display: flex; justify-content: space-between; }
        </style>
        </head><body>
        <div class="text-center border-bottom">
            <div class="bold" style="font-size: 16px">TGM ERP</div>
            <div class="mb-1">PRODUCTION TAG</div>
            <small>${new Date().toLocaleString()}</small>
        </div>
        <div class="mb-1"><strong>Product:</strong> ${productNama}</div>
        <div class="mb-1"><strong>Routing:</strong> #${routingUrutan}</div>
        <div class="mb-1"><strong>Operator:</strong> ${operatorNama}</div>
        <div class="mb-1"><strong>Mesin:</strong> ${mesinNama}</div>
        <div class="border-bottom"></div>
        <div class="flex mb-1"><span>Output:</span> <strong>${qtyOutput}</strong></div>
        <div class="flex mb-1">
            <span>Dimensi:</span> 
            <span>${parseFloat(panjangOutput)} ${parseFloat(lebarOutput) > 0 ? ('x ' + parseFloat(lebarOutput)) : (parseFloat(panjangOutput) > 0 ? 'M' : '')}</span>
        </div>
        <div class="border-bottom"></div>
        <div class="text-center" style="margin-top: 20px;">
             [QR CODE PLACEHOLDER]<br>
             #${detailId}
        </div>
        </body></html>
     `;
     
     let w = window.open('', '_blank', 'width=384,height=600');
     w.document.write(printContent);
     w.document.close();
     w.onload = function() { w.print(); w.close(); }
}

// ==========================================
// Misi 2: Chatter History Loader (ERP Level)
// ==========================================
function load_chatter_history(produksi_id) {
    $('#produksi_chatter_container').html('<div class="text-center p-3 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Memuat riwayat...</div>');
    $.ajax({
        url: '<?= base_url("wip/ajax_get_log") ?>',
        type: 'POST',
        dataType: 'json',
        data: { produksi_id: produksi_id },
        success: function(res) {
            if(res.success) {
                $('#produksi_chatter_container').html(res.html);
            } else {
                $('#produksi_chatter_container').html('<div class="text-danger text-center"><i class="fas fa-exclamation-triangle"></i> Gagal memuat log.</div>');
            }
        },
        error: function() {
            $('#produksi_chatter_container').html('<div class="text-danger text-center"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan koneksi server saat memuat log.</div>');
        }
    });
}

$(document).ready(function() {
    load_chatter_history(<?= $produksi->produksi_id ?? 0 ?>);
});
</script>
