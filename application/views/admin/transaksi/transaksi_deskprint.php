<!-- Transaksi Deskprint CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/transaksi-deskprint.css') ?>">
<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="user-info-modern mb-4">
            <img class="avatar"
                src="<?= !empty($user_profile['foto']) ? base_url('assets/images/karyawan/' . $user_profile['foto']) : base_url('assets/images/karyawan/default.png') ?>"
                alt="User Avatar">
            <div class="user-details">
                <h4><?= !empty($user_profile['nama_lengkap']) ? $user_profile['nama_lengkap'] : ($user_profile['username'] ?? 'User') ?>
                </h4>
                <div class="email"><i class="fas fa-envelope me-1"></i> <?= $user_profile['email'] ?? '-' ?></div>
                <span class="role-badge"><i class="fas fa-user-tag me-1"></i>
                    <?= $user_profile['role'] ?? ($user_profile['level'] ?? '-') ?></span>
                <span class="status-badge"><i class="fas fa-circle me-1"></i> Aktif</span>
            </div>
            <div class="summary-cards ms-auto">
                <div class="summary-card-modern">
                    <span class="icon"><i class="fas fa-calendar-check"></i></span>
                    <div>
                        <div class="label">Transaksi (Deskprint) Bulan Ini</div>
                        <div class="value"><?= $jml_transaksi_bulan_ini ?></div>
                    </div>
                </div>
                <div class="summary-card-modern">
                    <span class="icon total"><i class="fas fa-coins"></i></span>
                    <div>
                        <div class="label">Total Transaksi (Deskprint)</div>
                        <div class="value" style="color:#1a73e8;">Rp
                            <?= number_format($total_transaksi_user, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <hr class="divider-modern"> -->
        <div class="alert alert-warning">
            <i class="fas fa-info-circle me-2"></i>
            Transaksi Yang Dapat di Edit adalah Transaksi Yang Belum di Dibayar atau Belum di Proses Oleh Kasir
        </div>
        <div class="mb-3">
            <div class="btn-group" role="group" aria-label="Filter Status Pembayaran">
                <button type="button" class="btn btn-outline-primary"
                    onclick="window.location.href='<?= base_url('transaksi/createspk') ?>'">Kembali</button>
                <button type="button" class="btn btn-outline-primary active" id="filter-all">Semua</button>
                <button type="button" class="btn btn-outline-success" id="filter-sudah">Sudah Bayar</button>
                <button type="button" class="btn btn-outline-danger" id="filter-belum">Belum Bayar</button>
            </div>
        </div>
        <div class="card shadow-sm">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0" id="datatable-details">
                        <thead class="table-dark">
                            <tr>
                                <th>Nomor SPK</th>
                                <th>Tanggal<br>Jam Pemesanan</th>
                                <th>Nama<br>Pelanggan</th>
                                <th>WA<br>Pelanggan</th>
                                <th>Metode<br>Pemesanan</th>
                                <th>Jumlah<br>Item</th>
                                <th>Total<br>Transaksi</th>
                                <th>Status<br>Pembayaran</th>
                                <th hidden></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($tampilData as $dt) {
                                $status_bayar = $dt->status_pembayaran == 'pending' ? 'belum' : 'sudah';
                                if ($dt->status_pembayaran == 'pending') {
                                    $status_pembayaran = "<span class='text-danger'>Belum Bayar</span>";
                                    $base_url = base_url('transaksi/editSPK/' . $dt->nospk);
                                    $tombolspk = "<a class='btn btn-primary w-100' href='" . $base_url . "'>" . $dt->nospk . "</a>";
                                } else {
                                    $status_pembayaran = "<span class='text-success'>Sudah Bayar</span>";
                                    $tombolspk = "<a class='btn btn-dark w-100 disabled' href='#'>" . $dt->nospk . "</a>";
                                }

                                $total_bayar = $dt->bayar_tunai + $dt->bayar_debit + $dt->bayar_kartukredit + $dt->bayar_transfer + $dt->bayar_ewallet;
                                $kurang_bayar = $dt->grand_total - $total_bayar;
                                ?>
                                <tr data-status="<?= $status_bayar ?>">
                                    <td><?= $dt->nospk ?></td>
                                     <td>
                                        <?php
                                        // Set timezone ke Indonesia (WIB)
                                        $timezone_jakarta = new DateTimeZone('Asia/Jakarta');

                                        // Parse tanggal dari tbl_pemesanan.created_at dan konversi ke timezone Indonesia (WIB)
                                        // Waktu diambil dari field created_at pada tabel tbl_pemesanan
                                        // Field ini menunjukkan kapan transaksi/pemesanan dibuat
                                        try {
                                            // Gunakan created_at dari tbl_pemesanan
                                            $created_at = $dt->created_at ?? null;
                                            if (!empty($created_at)) {
                                                // Deteksi apakah string sudah mengandung timezone info
                                                $has_timezone = (preg_match('/[+-]\d{2}:?\d{2}$/', $created_at) ||
                                                    strpos($created_at, 'Z') !== false ||
                                                    stripos($created_at, 'UTC') !== false);

                                                if ($has_timezone) {
                                                    // Jika sudah ada timezone info, parse langsung
                                                    $datetime = new DateTime($created_at);
                                                } else {
                                                    // Jika tidak ada timezone info, asumsikan UTC (standar untuk database)
                                                    // created_at dari tbl_pemesanan biasanya dalam format Y-m-d H:i:s tanpa timezone
                                                    $datetime = new DateTime($created_at, new DateTimeZone('UTC'));
                                                }
                                                // Konversi ke timezone Jakarta (WIB)
                                                $datetime->setTimezone($timezone_jakarta);
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
                                         <div class="d-flex flex-column" style="min-width: 180px;">
                                            <?php if ($is_today): ?>
                                                <span class="badge bg-info mb-1" style="font-size: 0.7rem; width: fit-content;">
                                                    <i class="fas fa-calendar-day me-1"></i>Hari Ini
                                                </span>
                                            <?php elseif ($is_yesterday): ?>
                                                <span class="badge bg-secondary mb-1"
                                                    style="font-size: 0.7rem; width: fit-content;">
                                                    <i class="fas fa-calendar me-1"></i>Kemarin
                                                </span>
                                            <?php endif; ?>

                                            <div class="fw-semibold text-primary mb-1"
                                                style="line-height: 1.3; font-size: 0.9rem;">
                                                <i class="fas fa-calendar-alt me-1 text-muted"
                                                    style="font-size: 0.85rem;"></i>
                                                <?= $hari_nama ?>, <?= $tanggal ?>     <?= $bulan_nama ?>     <?= $tahun ?>
                                            </div>
                                            <div class="text-muted small d-flex align-items-center flex-wrap"
                                                style="gap: 0.25rem; line-height: 1.4;">
                                                <span class="d-flex align-items-center">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <strong class="text-dark"><?= $jam ?>:<?= $detik ?></strong>
                                                    <span class="ms-1 fw-bold text-primary">WIB</span>
                                                </span>
                                                <?php if ($show_relative && $diff_total_minutes < 60): ?>
                                                    <span class="badge bg-success" style="font-size: 0.65rem;">
                                                        <?= $diff_minutes > 0 ? $diff_minutes . ' menit lalu' : 'Baru saja' ?>
                                                    </span>
                                                <?php elseif ($show_relative && $diff_hours > 0 && $diff_hours < 24): ?>
                                                    <span class="badge bg-info" style="font-size: 0.65rem;">
                                                        <?= $diff_hours ?> jam lalu
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $dt->konsumen_nama ?></td>
                                    <td><?= $dt->konsumen_nohp ?></td>
                                    <td class="text-center">
                                        <div><?= $dt->nama_jenis_transaksi ?></div>
                                        <small><?= $dt->marketplace_nama ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $qdetail = $this->M_transaksi->getDetailPesan($dt->nospk)->result();
                                        $item = $this->M_transaksi->getDetailPesan($dt->nospk)->num_rows();
                                        ?>
                                        <?= $item ?>
                                    </td>

                                    <!-- <td class="text-end"><?= number_format($dt->grand_total ?? 0) ?></td> -->
                                    <td class="text-end">
                                        <?php
                                        // Coba beberapa kemungkinan nama kolom
                                        // Jika tetap 0, berarti data belum tersimpan di tabel utama (tbl_pemesanan)
                                        $val = $dt->grand_total ?? 0;
                                        if ($val == 0)
                                            $val = $dt->total_harga ?? 0;
                                        if ($val == 0)
                                            $val = $dt->total_pemesanan ?? 0;
                                        if ($val == 0)
                                            $val = $dt->total_bayar ?? 0;

                                        // Jika masih 0, cek apakah ada properti lain yg menyimpan total
                                        // echo json_encode($dt); // Uncomment baris ini untuk debug jika perlu
                                    
                                        echo number_format($val);
                                        ?>
                                    </td>
                                    <td class="text-center"><?= $status_pembayaran ?></td>
                                </tr>
                                <?php $no++;
                            } ?>
                        </tbody>
                    </table>
                </div> <!-- /.table-responsive -->
            </div> <!-- /.card-body -->
        </div> <!-- /.card -->
    </div>
</div>

<!-- Transaksi Deskprint JavaScript -->
<script src="<?= base_url('assets/js/transaksi-deskprint.js') ?>"></script>