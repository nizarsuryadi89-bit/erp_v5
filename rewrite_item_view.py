import sys

file_path = "application/views/admin/wip/components/v_table_item_view_core.php"
with open(file_path, "r") as f:
    content = f.read()

# 1. Replace thead
old_thead = """        <thead>
            <tr style="background: #fdfdfd; border-bottom: 2px solid #f1f5f9;">
                <th class="text-center <?= (isset($view_mode) && $view_mode === 'finishing') ? 'd-none' : '' ?>"
                    style="width: 40px; min-width: 40px; max-width: 40px;">
                    <div class="form-check d-flex justify-content-center m-0 p-0">
                        <input class="form-check-input check-all-nesting" type="checkbox"
                            style="cursor: pointer; width: 1.1rem; height: 1.1rem; margin-left: 0;">
                    </div>
                </th>
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
            </tr>
        </thead>"""

new_thead = """        <thead>
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
        </thead>"""
content = content.replace(old_thead, new_thead)


# 2. Replace tbody columns
# Let's find the start of Spk td
start_marker = "                    <td style=\"vertical-align: middle;\">\n                        <div class=\"d-flex flex-column gap-1\">\n                            <div class=\"spk-id-text text-nowrap\">"
end_marker = "                    <td style=\"vertical-align: top; padding-top: 0.75rem; padding-bottom: 0.75rem;\">\n                        <div class=\"d-flex flex-column gap-1\">\n                            <!-- 1. Header Row (Product Name + BOM Button + URGENT) -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker, start_idx)

if start_idx == -1 or end_idx == -1:
    print("Could not find markers for tbody (part 1)!")
    sys.exit(1)

new_tbody_cols_1 = """                    <!-- Pesanan (SPK) -->
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

                            <div class="mt-2 text-center">
                                <?php if (isset($filter_status_view) && $filter_status_view === 'konsumen'): ?>
                                    <span class="badge bg-secondary text-white shadow-sm border border-secondary border-opacity-25" style="font-size: 0.65rem; padding: 3px 6px;">
                                        <i class="fas fa-barcode me-1"></i> <?= htmlspecialchars($item->produksi_id) ?>
                                    </span>
                                <?php else: ?>
                                    <button type="button"
                                        class="btn-action-premium btn-primary-p py-1 px-2 w-100" style="font-size: 0.65rem;"
                                        onclick="previewTagSPK('<?= htmlspecialchars($item->produksi_id) ?>')"
                                        title="Cetak Tag (Alt+C)">
                                        <i class="fas fa-tag me-1"></i>Cetak Tag
                                    </button>
                                <?php endif; ?>
                            </div>
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
                                <?php if ($item->perusahaan_id != $this->session->userdata('perusahaan_id')): ?>
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill pulse-red mt-1"
                                        style="padding: 3px 6px; font-size: 0.55rem; display: block; width: fit-content;">
                                        <i class="fas fa-exchange-alt me-1"></i>TITIPAN DARI
                                        <?= strtoupper($item->nama_perusahaan ?? 'CABANG LAIN') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>\n\n"""

content = content[:start_idx] + new_tbody_cols_1 + content[end_idx:]


# 3. Remove Metode and Lokasi columns
start_marker2 = "                    <td class=\"text-center\" style=\"vertical-align: middle;\">\n                        <?php if (!empty($item->nama_jenis_transaksi)): ?>"
end_marker2 = "                    <td class=\"text-center\" style=\"vertical-align: middle;\">\n                        <?php if (!empty($item->nama_perusahaan)): ?>"
end_marker3 = "                    <td style=\"vertical-align: middle;\">\n                        <?php"

# Find first column to remove (Metode Pesanan)
idx2_1 = content.find(start_marker2)
if idx2_1 != -1:
    idx2_2 = content.find(end_marker2, idx2_1)
    if idx2_2 != -1:
        # Find next TD (Lokasi Pesanan) and remove it too
        idx3_1 = idx2_2
        idx3_2 = content.find(end_marker3, idx3_1)
        if idx3_2 != -1:
            content = content[:idx2_1] + content[idx3_2:]
        else:
            print("Warning: Could not find end marker for Lokasi!")
    else:
        print("Warning: Could not find end marker for Metode!")
else:
    print("Warning: Could not find start marker for Metode!")


with open(file_path, "w") as f:
    f.write(content)

print("UI Rewrite script applied to Item Core View successfully!")
