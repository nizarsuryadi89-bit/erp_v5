<div class="table-responsive">
    <table id="datatable-details" class="table table-bordered table-hover table-modern align-middle mb-0">
        <thead class="align-middle">
            <tr>
                <th class="text-center" style="width: 50px;">NO</th>
                <th class="text-center" style="width: 130px;">SPK</th>
                <th>TANGGAL JAM PEMESANAN</th>
                <th>NAMA PELANGGAN</th>
                <th>DESKPRINT</th>
                <th>METODE PENJUALAN</th>
                <th class="text-center" style="width: 100px;">JUMLAH ITEM</th>
                <th class="text-end" style="width: 140px;">TOTAL TRANSAKSI</th>
                <th class="text-center" style="width: 200px;">ACTION</th>
                <th class="d-none"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $transaksi_belumbayar = is_array($transaksi_belumbayar) ? $transaksi_belumbayar : [];

            // Define user role/permissions outside loop
            $current_user_id = $this->session->userdata('id');
            $user_role = (int) $this->session->userdata('level');
            $is_privileged = in_array($user_role, [1, 3, 17, 22]); // Admin, Kasir, Owner, Developer
            $is_deskprint = ($user_role === 2);

            foreach ($transaksi_belumbayar as $dt):
                // Filter: Jika Deskprint, hanya tampilkan SPK milik sendiri
                if ($is_deskprint && $dt->deskprint_id != $current_user_id) {
                    continue;
                }
                ?>
                <?php
                $status_pembayaran = $dt->status_pembayaran == 'pending'
                    ? "<span class='badge-status badge-belum'><i class='fas fa-exclamation-circle me-1'></i>Belum Bayar</span>"
                    : "<span class='badge-status badge-sudah'><i class='fas fa-check-circle me-1'></i>Sudah Bayar</span>";

                $total_transaksi = $dt->total_transaksi;
                $item = $dt->jumlah_item;

                $tr = '';
                switch ($dt->status) {
                    case '1':
                        $tr = 'table-danger';
                        break;
                    case '2':
                        $tr = 'table-warning';
                        break;
                    case '3':
                        $tr = 'table-success';
                        break;
                    case '4':
                        $tr = 'table-info';
                        break;
                    case '5':
                        $tr = 'table-secondary';
                        break;
                    case '6':
                        $tr = 'table-dark';
                        break;
                }
                ?>
                <tr class="<?= $tr ?> <?= (!empty($dt->is_blacklist) && $dt->is_blacklist == 1) ? 'table-danger border-start border-4 border-danger' : '' ?>" data-metode="<?= html_escape(strtolower($dt->nama_jenis_transaksi ?? '-')) ?>">
                    <td class="text-center fw-semibold text-muted" style="font-size: 0.95rem;"><?= $no ?></td>
                    <td class="text-center" style="vertical-align: middle;">
                        <span class="spk-id-text"><?= html_escape($dt->nospk) ?></span>
                        <?php
                        $lokasi = $dt->lokasi_cabang ?: 'PUSAT';
                        $display_lokasi = $lokasi;
                        if (stripos($lokasi, 'Merch') !== false || stripos($lokasi, 'Cabang') !== false) {
                            $badge_class = 'bg-warning text-dark border-warning';
                            $icon_color = 'text-dark';
                            $display_lokasi = 'TGM MERCH';
                        } elseif (stripos($lokasi, 'Official') !== false || stripos($lokasi, 'Pusat') !== false || stripos($lokasi, 'Print') !== false) {
                            $badge_class = 'bg-primary text-white border-primary';
                            $icon_color = 'text-white';
                            $display_lokasi = 'TGM PUSAT';
                        } else {
                            $badge_class = 'bg-light text-dark border';
                            $icon_color = 'text-primary';
                        }
                        $is_titipan = ($dt->perusahaan_id != $dt->id_lokasi_cetak);
                        ?>
                        <div class="mt-1 d-flex flex-column align-items-center gap-1">
                            <span class="badge <?= $badge_class ?> shadow-sm" style="font-size: 0.65rem; padding: 4px 6px;">
                                <i class="fas fa-building <?= $icon_color ?> me-1"></i><?= html_escape($display_lokasi) ?>
                            </span>
                            <?php if ($is_titipan): ?>
                                <span class="badge bg-danger text-white shadow-sm" style="font-size: 0.60rem; padding: 3px 6px;">
                                    <i class="fas fa-handshake me-1"></i>TITIPAN
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td data-order="<?= strtotime($dt->tgl_pemesanan ?? $dt->created_at) ?>">
                        <?php
                        // Set timezone ke Indonesia (WIB)
                        $timezone_jakarta = new DateTimeZone('Asia/Jakarta');

                        // Parse tanggal dari database dan konversi ke timezone Indonesia (WIB)
                        try {
                            $raw_date = $dt->tgl_pemesanan ?? $dt->created_at;
                            if (!empty($raw_date)) {
                                // Deteksi apakah string sudah mengandung timezone info
                                $has_timezone = (preg_match('/[+-]\d{2}:?\d{2}$/', $raw_date) ||
                                    strpos($raw_date, 'Z') !== false ||
                                    stripos($raw_date, 'UTC') !== false);

                                if ($has_timezone) {
                                    $datetime = new DateTime($raw_date);
                                    $datetime->setTimezone($timezone_jakarta);
                                } else {
                                    // Jika tidak ada timezone info, asumsikan sudah dalam Asia/Jakarta (WIB)
                                    // Agar tidak terjadi selisih jam jika database sudah lokal
                                    $datetime = new DateTime($raw_date, $timezone_jakarta);
                                }
                            } else {
                                throw new Exception('Empty date');
                            }
                        } catch (Exception $e) {
                            // Fallback jika parsing gagal - gunakan waktu sekarang
                            $datetime = new DateTime('now', $timezone_jakarta);
                        }

                        // Format tanggal Indonesia
                        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                        $tanggal = $datetime->format('d');
                        $bulan_nama = $bulan[(int) $datetime->format('m')];
                        $tahun = $datetime->format('Y');
                        $hari_nama = $hari[(int) $datetime->format('w')];
                        $jam = $datetime->format('H:i');
                        $detik = $datetime->format('s');

                        // Cek apakah hari ini atau kemarin
                        $sekarang = new DateTime('now', $timezone_jakarta);
                        $kemarin = clone $sekarang;
                        $kemarin->modify('-1 day');

                        $tanggal_str = $datetime->format('Y-m-d');
                        $is_today = $tanggal_str === $sekarang->format('Y-m-d');
                        $is_yesterday = $tanggal_str === $kemarin->format('Y-m-d');

                        // Hitung selisih waktu untuk informasi relatif (hanya untuk hari ini)
                        $diff_total_minutes = 0;
                        $diff_hours = 0;
                        $diff_minutes = 0;
                        $show_relative = false;

                        if ($is_today) {
                            $diff = $sekarang->diff($datetime);
                            $diff_total_minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                            $diff_hours = floor($diff_total_minutes / 60);
                            $diff_minutes = $diff_total_minutes % 60;

                            // Tampilkan relatif waktu jika kurang dari 24 jam dan tidak negatif
                            if ($diff_total_minutes >= 0 && $diff_total_minutes < 1440) {
                                $show_relative = true;
                            }
                        }
                        ?>

                        <div class="d-flex flex-column gap-1" style="min-width: 190px;">
                            <?php if ($is_today): ?>
                                <span class="badge badge-hari-ini text-uppercase">
                                    <i class="fas fa-calendar-day"></i> HARI INI
                                </span>
                            <?php elseif ($is_yesterday): ?>
                                <span class="badge badge-kemarin text-uppercase">
                                    <i class="fas fa-calendar"></i> KEMARIN
                                </span>
                            <?php endif; ?>

                            <div class="text-dark fw-bold"
                                style="font-size: 0.85rem; display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                <i class="far fa-calendar-alt text-muted" style="font-size: 0.9rem;"></i>
                                <span><?= $hari_nama ?>, <?= $tanggal ?>     <?= $bulan_nama ?>     <?= $tahun ?></span>
                            </div>

                            <div class="text-muted d-flex align-items-center gap-1 flex-wrap"
                                style="font-size: 0.8rem; margin-top: 1px;">
                                <span class="d-flex align-items-center gap-1">
                                    <i class="far fa-clock text-muted"></i>
                                    <strong class="text-dark"><?= $jam ?>:<?= $detik ?></strong>
                                    <span class="fw-bold text-primary" style="font-size: 0.75rem;">WIB</span>
                                </span>
                                <?php if ($show_relative && $diff_total_minutes < 60): ?>
                                    <span class="badge badge-relative-time bg-success ms-1">
                                        <?= strtoupper($diff_minutes > 0 ? $diff_minutes . ' menit lalu' : 'Baru saja') ?>
                                    </span>
                                <?php elseif ($show_relative && $diff_hours > 0 && $diff_hours < 24): ?>
                                    <span class="badge badge-relative-time bg-info ms-1">
                                        <?= strtoupper($diff_hours . ' jam lalu') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                            <span class="fw-bold text-dark text-uppercase"
                                style="font-size: 0.9rem; letter-spacing: 0.3px;"><?= html_escape($dt->konsumen_nama) ?></span>
                            <?php if (!empty($dt->is_blacklist) && $dt->is_blacklist == 1): ?>
                                <span class="badge bg-danger text-white shadow-sm" style="font-size: 0.65rem; padding: 3px 6px;" title="<?= !empty($dt->catatan_blacklist) ? html_escape($dt->catatan_blacklist) : 'Konsumen Pengawasan Kasir' ?>">
                                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>PENGAWASAN KASIR
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($dt->is_blacklist) && $dt->is_blacklist == 1 && !empty($dt->catatan_blacklist)): ?>
                            <div class="alert alert-danger py-1 px-2 mb-1 rounded-2" style="font-size: 0.72rem; line-height: 1.2;">
                                <i class="fas fa-info-circle me-1"></i><strong>Catatan:</strong> <?= html_escape($dt->catatan_blacklist) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($dt->konsumen_nohp)): ?>
                            <div class="text-muted small d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <i class="fab fa-whatsapp text-success" style="font-size: 0.85rem;"></i>
                                <span><?= html_escape($dt->konsumen_nohp) ?></span>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <span class="fw-semibold text-dark"
                            style="font-size: 0.85rem;"><?= html_escape($dt->nama_lengkap) ?></span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1 text-dark" style="font-size: 0.85rem;">
                            <i class="fas fa-shopping-cart text-muted" style="font-size: 0.8rem; min-width: 14px;"></i>
                            <span><?= html_escape($dt->nama_jenis_transaksi ?: '-') ?></span>
                        </div>
                        <?php if ($dt->id_jenis_transaksi == 2 && !empty($dt->marketplace_nama)): ?>
                            <div class="text-muted small mt-1 d-flex align-items-center gap-1"
                                style="font-size: 0.7rem; padding-left: 18px;">
                                <i class="fas fa-store" style="font-size: 0.7rem;"></i>
                                <span><?= html_escape($dt->marketplace_nama) ?></span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center fw-semibold text-dark fs-6" style="vertical-align: middle;">
                        <?= $item ?>
                    </td>
                    <td class="text-end fw-bold text-dark fs-6" style="vertical-align: middle;">
                        Rp <?= number_format($total_transaksi) ?>
                    </td>
                    <td class="text-center table-action" style="vertical-align: middle;">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <?php
                            $is_pending = ($dt->status_pembayaran === 'pending');
                            // Kasir is restricted if they are NOT in the production branch AND NOT in the origin branch
                            $current_perusahaan_id = $this->session->userdata('perusahaan_id');
                            $is_kasir_restricted = ($current_perusahaan_id != $dt->id_lokasi_cetak && $current_perusahaan_id != $dt->perusahaan_id); 
                            ?>

                            <?php if ($is_pending): ?>
                                <!-- --- AKSI BELUM BAYAR (PENDING) --- -->
                                <?php if ($is_privileged): ?>
                                    <!-- Tombol Bayar & Preview (Kasir/Admin/Owner) -->
                                    <?php if (!$is_kasir_restricted): ?>
                                        <a href="<?= base_url($controller . '/bayar/' . encrypt_url($dt->nospk)) ?>"
                                            class="btn-action-premium btn-action-pay" title="Bayar">
                                            <i class="fas fa-money-bill"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn-action-premium btn-action-preview"
                                        onclick="previewTransaksi('<?= html_escape($dt->nospk) ?>')" title="Preview Transaksi">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if (($is_deskprint || $is_privileged) && !$is_kasir_restricted): ?>
                                    <!-- Tombol Edit/Tarik Balik (Deskprint Milik Sendiri atau Privileged) -->
                                    <?php if ($is_privileged || $dt->deskprint_id == $current_user_id): ?>
                                        <button type="button" class="btn-action-premium btn-action-edit" title="Tarik Balik & Edit"
                                            onclick="tarikBalikSPK('<?= html_escape($dt->nospk) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($is_privileged): ?>
                                    <!-- Tombol Link & Delete (Kasir/Admin/Owner Only) -->
                                    <a href="<?= base_url('cekpesanan/spk/' . urlencode(encrypt_spk($dt->nospk))) ?>"
                                        target="_blank" class="btn-action-premium btn-action-link" title="Buka Link Invoice">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <?php if (!$is_kasir_restricted): ?>
                                        <button type="button" class="btn-action-premium btn-action-delete" title="Hapus Permanen"
                                            onclick="hapusSPK('<?= html_escape($dt->nospk) ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php else: ?>
                                <!-- --- AKSI SUDAH BAYAR (PAID) --- -->
                                <?php if ($is_privileged): ?>
                                    <a href="<?= html_escape(base_url($controller . '/cetak_invoice_ulang/' . $dt->nospk)) ?>"
                                        class="btn-action-premium btn-action-pay" title="Cetak Ulang Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="<?= base_url('cekpesanan/spk/' . urlencode(encrypt_spk($dt->nospk))) ?>"
                                        target="_blank" class="btn-action-premium btn-action-link" title="Link">
                                        <i class="fas fa-link"></i>
                                    </a>
                                    <button type="button" class="btn-action-premium btn-action-preview"
                                        onclick="previewTransaksi('<?= html_escape($dt->nospk) ?>')" title="Lihat Detail">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="d-none"></td>
                </tr>
                <?php
                $no = $no + 1;
            endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .bg-light-warning {
        background-color: #fff3cd !important;
    }

    .bg-light-warning:hover {
        background-color: #ffeeba !important;
    }

    /* Modern Table styling matching premium screenshot */
    .table-modern {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100%;
        border: 1px solid #eef2f5 !important;
        border-radius: 8px;
        overflow: hidden;
    }

    .table-modern thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        font-size: 0.7rem !important;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0 !important;
        border-top: none !important;
        padding: 14px 16px !important;
        vertical-align: middle;
    }

    .table-modern tbody tr {
        transition: all 0.15s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8fafc !important;
    }

    .table-modern tbody td {
        padding: 14px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        border-right: 1px solid #f1f5f9 !important;
        vertical-align: middle;
        color: #334155;
    }

    .table-modern tbody td:last-child {
        border-right: none !important;
    }

    /* Style for SPK ID */
    .spk-id-text {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    /* Style for premium badges */
    .badge-hari-ini {
        background-color: #00a2b9 !important;
        color: #ffffff !important;
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
        display: inline-flex;
        align-items: center;
        width: fit-content;
        gap: 4px;
        letter-spacing: 0.5px;
    }

    .badge-kemarin {
        background-color: #64748b !important;
        color: #ffffff !important;
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
        display: inline-flex;
        align-items: center;
        width: fit-content;
        gap: 4px;
        letter-spacing: 0.5px;
    }

    .badge-relative-time {
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-block;
        vertical-align: middle;
    }

    .badge-relative-time.bg-success {
        background-color: #28a745 !important;
        color: #ffffff !important;
    }

    .badge-relative-time.bg-info {
        background-color: #00a2b9 !important;
        color: #ffffff !important;
    }

    /* Action buttons matching screenshot */
    .btn-action-premium {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        border: none;
        transition: all 0.2s ease;
        padding: 0;
        margin: 0 1px;
        text-decoration: none !important;
    }

    .btn-action-premium i {
        font-size: 0.85rem;
    }

    .btn-action-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0.95;
    }

    /* Pay Button (slate/navy dark) */
    .btn-action-pay {
        background-color: #2b3a4a !important;
        color: #ffffff !important;
    }

    /* Preview Button (gold warning) */
    .btn-action-preview {
        background-color: #ffb703 !important;
        color: #ffffff !important;
    }

    /* Edit/Recall Button (cream ivory warning background, brown icon) */
    .btn-action-edit {
        background-color: #fef3c7 !important;
        color: #b45309 !important;
    }

    /* Link/External Link Button (cyan) */
    .btn-action-link {
        background-color: #00c0ef !important;
        color: #ffffff !important;
    }

    /* Delete Button (red) */
    .btn-action-delete {
        background-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .btn-sm {
        padding: 0.4rem 0.6rem;
    }
</style>


<!-- REMOVED REDUNDANT SCRIPTS - NOW CENTRALIZED IN MAIN VIEW -->

<!-- <script>
    setInterval(function() {
        location.reload();
    }, 10000);
</script> -->