
<div class="container py-4">
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <h5 class="card-title fw-bold">Laporan Laba - Rugi Perusahaan</h5>
            <p class="card-text">Monitoring Laba Rugi</p>
            <p class="text-muted mb-0">Tanggal <?= date_indo(date('Y-m-d')) ?></p>
        </div>
    </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Keterangan</th>
                        <th class="text-end">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_pendapatan = 0;
                    $total_beban = 0;
                    ?>
                    <tr>
                        <th colspan="2">Total Pendapatan / Penjualan</th>
                    </tr>
                   
                    <tr class="fw-bold">
                        <td>Total Laba Penjualan</td>
                        <td class="text-end"><?= number_format($laba['laba'], 0, ',', '.') ?></td>
                    </tr>

                    <tr class="fw-bold">
                        <td>Total Pendapatan</td>
                        <td class="text-end"><?= number_format($pendapatan['total_pendapatan'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">Pengeluaran</th>
                    </tr>
                   
                    <tr class="fw-bold">
                        <td>- Total Pengeluaran Bulanan</td>
                        <td class="text-end"><?= number_format($pengeluaran_bulanan['pengeluaran_bulanan'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td>- Total Pengeluaran harian</td>
                        <td class="text-end"><?= number_format($pengeluaran_harian['pengeluaran_harian'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="table-success fw-bold">
                        <td>Laba/Rugi Bersih</td>
                        <td class="text-end"><?= number_format($pendapatan['total_pendapatan'] - ($pengeluaran_bulanan['pengeluaran_bulanan'] + $pengeluaran_harian['pengeluaran_harian']), 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
