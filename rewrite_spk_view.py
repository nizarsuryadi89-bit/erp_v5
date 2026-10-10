import sys

file_path = "application/views/admin/wip/components/v_table_spk_view.php"
with open(file_path, "r") as f:
    content = f.read()

# 1. Replace thead
old_thead = """        <thead class="bg-light text-muted">
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
        </thead>"""

new_thead = """        <thead class="bg-light text-muted">
            <tr>
                <th class="text-center" width="40">No.</th>
                <th width="160">Pesanan (SPK)</th>
                <th width="150">Pelanggan & Asal</th>
                <th>Produk</th>
                <th width="200" class="text-center">Status Pesanan</th>
                <th width="150" class="text-center">Tindakan</th>
                <th class="d-none">StatusTags</th>
            </tr>
        </thead>"""
content = content.replace(old_thead, new_thead)


# 2. Replace tbody columns (from Spk to Lokasi Pesanan)
# Let's find the start of Spk td
start_marker = "                    <!-- Spk -->\n                    <td>\n                        <div class=\"d-flex flex-column gap-1\">"
end_marker = "                    <!-- Status Pesanan -->"

start_idx = content.find("                    <!-- Spk -->")
end_idx = content.find(end_marker, start_idx)

if start_idx == -1 or end_idx == -1:
    print("Could not find markers for tbody!")
    sys.exit(1)

new_tbody_cols = """                    <!-- Pesanan (SPK) -->
                    <td style="vertical-align: top; padding-top: 0.75rem;">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="spk-code">
                                    <?= $p->nospk ?>
                                </span>
                                <?php if ($isReprintGroup): ?>
                                    <span class="reprint-indicator"><i class="fas fa-redo"></i> REPRINT</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Tanggal & Deadline -->
                            <div class="small" style="font-size: 0.75rem;">
                                <?php if ($p->tgl_spk): ?>
                                    <div class="text-dark"><i class="far fa-clock me-1 text-muted"></i> <?= date('d/m/Y H:i', strtotime($p->tgl_spk)) ?></div>
                                <?php endif; ?>
                                
                                <?php if (!empty($p->dateline_tgl) && $p->dateline_tgl != '0000-00-00'): ?>
                                    <div class="text-danger fw-bold mt-1"><i class="fas fa-hourglass-half me-1"></i> <?= date('d/m/Y', strtotime($p->dateline_tgl)) ?> <?= !empty($p->dateline_jam) ? date('H:i', strtotime($p->dateline_jam)) : '' ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-2">
                                <button class="btn-action-premium btn-primary-p py-1 px-2 w-100" style="font-size: 0.65rem;"
                                    onclick="previewTagSPK('<?= implode(',', array_column($group_items, 'produksi_id')) ?>')" title="Cetak Tag Seluruh SPK"><i
                                        class="fas fa-tags me-1"></i>Cetak Tag Batch</button>
                            </div>
                        </div>
                    </td>

                    <!-- Pelanggan & Asal -->
                    <td style="vertical-align: top; padding-top: 0.75rem;">
                        <div class="d-flex flex-column gap-2">
                            <div class="fw-800 text-dark text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                                <?= $p->konsumen_nama ?: '-' ?>
                            </div>
                            
                            <div>
                                <?php if (!empty($p->nama_jenis_transaksi)): ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 shadow-sm fw-900" style="font-size: 0.65rem;">
                                        <i class="fas fa-shopping-cart me-1"></i> <?= strtoupper($p->nama_jenis_transaksi) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-1">
                                <div class="company-pill"
                                    style="color: <?= $branchColor ?>; border: 1px solid <?= $branchColor ?>20; font-size: 0.65rem;">
                                    <div class="company-dot" style="background: <?= $branchColor ?>"></div>
                                    <?= $comp_name ?>
                                </div>
                                <?php if ($p->perusahaan_id != $this->session->userdata('perusahaan_id')): ?>
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill pulse-red mt-1"
                                        style="padding: 3px 6px; font-size: 0.55rem; display: block; width: fit-content;">
                                        <i class="fas fa-exchange-alt me-1"></i>TITIPAN DARI
                                        <?= strtoupper($p->nama_perusahaan ?? 'CABANG LAIN') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <!-- Produk -->
                    <td style="vertical-align: top; padding-top: 0.75rem;">
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
                                    $lines = explode("\\n", $p->catatan);
                                    foreach ($lines as $line) {
                                        if (preg_match('/\\[(?:BULK )?HANDOVER\\].*?\\|\\s*Oleh:\\s*(.*?)\\s*\\|\\s*Via:\\s*(.*?)\\s*\\|\\s*Note:\\s*(.*)/i', $line, $matches)) {
                                            $pb_oleh = trim($matches[1]);
                                            $pb_via = trim($matches[2]);
                                            $pb_note = trim($matches[3]);
                                            if ($pb_note === '-') $pb_note = null;
                                        } elseif (preg_match('/\\[PB\\].*?Dikirim ke Hub Pengambilan Barang/i', $line)) {
                                            // Skip noisy line
                                        } else {
                                            $clean_catatan[] = $line;
                                        }
                                    }
                                }
                                $final_catatan = trim(implode("\\n", $clean_catatan));
                            ?>
                            <?php if (!empty($final_catatan) || !empty($spk_op_cetak) || !empty($spk_op_finishing) || !empty($pb_oleh) || !empty($p->deskprint_nama)): ?>
                                <div class="mt-1 ps-2 border-start border-3 border-warning py-1 bg-warning bg-opacity-10 rounded-end"
                                    style="max-width:300px; white-space: normal;">
                                    <div class="small fw-bold text-warning-emphasis mb-1" style="font-size: 0.6rem;"><i
                                            class="fas fa-sticky-note me-1"></i>KETERANGAN:</div>
                                    
                                    <?php if (!empty($spk_op_cetak) || !empty($spk_op_finishing) || !empty($pb_oleh) || !empty($p->deskprint_nama)): ?>
                                        <div class="d-flex flex-wrap gap-2" style="font-size: 0.65rem;">
                                            <?php if (!empty($p->deskprint_nama)): ?>
                                                <div class="text-muted"><i class="fas fa-user-edit me-1"></i><span class="fw-bold text-dark"><?= htmlspecialchars($p->deskprint_nama) ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($spk_op_cetak)): ?>
                                                <div class="text-muted"><i class="fas fa-print me-1"></i><span class="fw-bold text-dark"><?= implode(', ', $spk_op_cetak) ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($spk_op_finishing)): ?>
                                                <div class="text-muted"><i class="fas fa-cut me-1"></i><span class="fw-bold text-dark"><?= implode(', ', $spk_op_finishing) ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($pb_oleh)): ?>
                                                <div class="text-muted"><i class="fas fa-handshake me-1"></i>PB: <span class="fw-bold text-dark"><?= htmlspecialchars($pb_oleh) ?></span> (<?= htmlspecialchars($pb_via) ?>)</div>
                                                <?php if (!empty($pb_note)): ?>
                                                    <div class="text-muted"><i class="fas fa-comment-dots me-1"></i><span class="fst-italic text-dark"><?= htmlspecialchars($pb_note) ?></span></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($final_catatan)): ?>
                                        <div class="text-dark small lh-sm fst-italic mt-1" style="font-size: 0.75rem;"><i class="fas fa-quote-left text-warning opacity-50 me-1"></i><?= nl2br(htmlspecialchars($final_catatan)) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>\n\n"""

content = content[:start_idx] + new_tbody_cols + content[end_idx:]

with open(file_path, "w") as f:
    f.write(content)

print("UI Rewrite script applied to SPK view successfully!")
