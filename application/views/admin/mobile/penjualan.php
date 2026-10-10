<div class="container py-4">

    <?php 
    // Pastikan model diload sebelum view
    $this->load->model('M_transaksi');

    $totalstore1 = 0;
    $totalstore2 = 0;
?>

    <div class="profile-card">
        <h6 class="text-success mb-3">
            <i class="bi bi-person-badge-fill me-2"></i>
            Transaksi  Bulan <?= date_indo(date('Y-m')) ?>
        </h6>
        <div class="table-wrapper-desktop">
            <!-- Tabel asli kamu tetap tampil di sini untuk desktop -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-none" id="datatable-details">		
                            
                    <thead>
                        <tr>
                            <th>Tanggal<br>Jam Pemesanan</th>
                            <th>Nama<br>Pelanggan</th>
                            <th>Metode<br>Pemesanan</th>
                            <th>Jumlah<br>Item</th>
                            <th>Total<br>Transaksi</th>
                            <th>Status<br>Pembayaran</th>
                            <th>Store</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $no =1;
                            foreach ($transaksi_sudahbayar as $dt){
                                if ($dt->status_pembayaran == 'pending'){
                                    $status_pembayaran = "<p class='text-danger'> Belum Bayar</p>";
                                    $base_url          = base_url('transaksi/createspk/'.$dt->nospk);
                                    $tombolspk         = "<a class='btn btn-primary btn-block' href='".$base_url."'>$dt->nospk</a>";
                                }else{
                                    $status_pembayaran = "<p class='text-success'> Sudah Bayar</p>";
                                    $tombolspk         = "<a class='btn btn-dark btn-block' href='#' disabled> $dt->nospk </a>";
                                }

                            $total_bayar                = $dt->bayar_tunai + $dt->bayar_debit + $dt->bayar_kartukredit + $dt->bayar_transfer + $dt->bayar_ewallet;
                            $kurang_bayar               = $dt->grand_total - $total_bayar;
                        ?>
                        <tr>
                            <td>
                                <?= $dt->nospk ?><br>
                                <?= date_indo($dt->created_at) ?><br>
                                <p><?= date('H:i', strtotime($dt->created_at)) ?></p>
                            </td>
                            <td>
                                Nama : <?= $dt->konsumen_nama ?> <br>
                                WA : <?= $dt->konsumen_nohp ?>
                            </td>
                            <td class="text-center">
                                <p><?= $dt->nama_jenis_transaksi ?></p>
                                <?= $dt->marketplace_nama ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                    $qdetail    = $this->M_transaksi->getDetailPesan($dt->nospk)->result();
                                    $item       = $this->M_transaksi->getDetailPesan($dt->nospk)->num_rows();
                                ?>
                                <?= $item  ?>
                            </td>
                            <td class="text-right">
                                <?= number_format($dt->total_penjualan ?? 0) ?>
                            </td>
                            <td class="text-center">
                                <?= $status_pembayaran ?>
                            </td>
                            <td>
                                <?= $dt->nama_perusahaan ?>
                            </td>
                        </tr>
                        <?php 
                            $no++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive-mobile">
            <?php foreach ($transaksi_sudahbayar as $dt):
                                        $status = ($dt->status_pembayaran == 'pending') ? "<span class='badge bg-danger'>Belum Bayar</span>" : "<span class='badge bg-success'>Sudah Bayar</span>";
                $total_bayar = $dt->bayar_tunai + $dt->bayar_debit + $dt->bayar_kartukredit + $dt->bayar_transfer + $dt->bayar_ewallet;
                $item = $this->M_transaksi->getDetailPesan($dt->nospk)->num_rows();
            ?>
            <div class="card mb-2 shadow-sm">
                <div class="card-body p-2">
                                            <h6><?= $dt->nospk ?> - <?= date_indo($dt->created_at) ?></h6>
                    <p class="mb-1"><strong>Nama:</strong> <?= $dt->konsumen_nama ?> <br>
                    <strong>WA:</strong> <?= $dt->konsumen_nohp ?></p>
                                            <p class="mb-1"><strong>Item:</strong> <?= $item ?> | <strong>Total:</strong> Rp <?= number_format($dt->total_penjualan ?? 0) ?></p>
                    <p class="mb-1"><?= $status ?> | <?= $dt->nama_perusahaan ?></p>
                    <small class="text-muted">Metode: <?= $dt->nama_jenis_transaksi ?> <?= $dt->marketplace_nama ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        
    </div>
</div>

<script>
  // Reload halaman setiap 30 detik (30000 ms)
  setInterval(function() {
    location.reload();
  }, 30000);
</script>
