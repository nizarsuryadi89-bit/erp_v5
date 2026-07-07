<div class="container-fluid my-4">
    <?php
    if (!function_exists('invoice_detail_metadata')) {
        function invoice_detail_metadata($item)
        {
            if (empty($item->metadata) || !is_string($item->metadata)) {
                return [];
            }

            $decoded = json_decode($item->metadata, true);
            return is_array($decoded) ? $decoded : [];
        }

        function invoice_detail_price($item)
        {
            $metadata = invoice_detail_metadata($item);
            if (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
                return (float) $metadata['unit_price'];
            }

            return (float) ($item->harga_1 ?? 0);
        }

        function invoice_detail_total($item, $harga = null)
        {
            $metadata = invoice_detail_metadata($item);
            if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
                return (float) $metadata['subtotal'];
            }

            $harga = $harga === null ? invoice_detail_price($item) : (float) $harga;
            $qty = (float) ($item->qty ?? 0);
            $panjang = (float) ($item->panjang ?? 0);
            $lebar = (float) ($item->lebar ?? 0);

            if ($panjang > 0 && $lebar > 0) {
                return round($panjang * $lebar * $harga * $qty);
            }

            return $harga * $qty;
        }
    }
    ?>
    <div class="row">
        <div class="col-12">
            <!-- Panel wrapper -->
            <div class="card">
                <div class="card-body">

                    <!-- Tombol aksi (jika diperlukan, uncomment) -->
                    <!--
                    <div class="mb-3">
                        <a href="<?= base_url('transaksi/add/1/1'); ?>" class="btn btn-primary">Tambah Transaksi <?= $controller ?></a>
                        <a href="<?= base_url($controller . '/formAdd') ?>" class="btn btn-success">
                            Cetak <?= $controller ?> <i class="fa fa-print"></i>
                        </a>
                    </div>
                    -->

                    <!-- Tabel Transaksi -->
                    <div class="table-responsive">
                        <table id="datatable-details" class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nomor SPK</th>
                                    <th>Tanggal<br>Jam Pemesanan</th>
                                    <th>Nama<br>Pelanggan</th>
                                    <th>Metode<br>Pemesanan</th>
                                    <th>Jumlah Item</th>
                                    <th>Total<br>Transaksi</th>
                                    <th>Status<br>Pembayaran</th>
                                    <th>Total<br>Bayar</th>
                                    <th>Kurang<br>Bayar</th>
                                    <th>Pengguna</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksi_sudahbayar as $dt):
                                    $status_pembayaran = ($dt->status_pembayaran == 'pending')
                                        ? "<span class='text-danger'>Belum Bayar</span>"
                                        : "<span class='text-success'>Sudah Bayar</span>";

                                    $total_bayar = $dt->bayar_tunai + $dt->bayar_debit + $dt->bayar_kartukredit + $dt->bayar_transfer + $dt->bayar_ewallet;

                                    $qdetail = $this->M_transaksi->getDetailPesan($dt->nospk)->result();
                                    $item = count($qdetail);

                                    // Calculate real total from details to ensure PxL is accounted for
                                    $real_total = 0;
                                    foreach ($qdetail as $item_dt) {
                                        $harga_satuan = invoice_detail_price($item_dt);
                                        $real_total += invoice_detail_total($item_dt, $harga_satuan);
                                    }

                                    $kurang_bayar = $real_total - $total_bayar;

                                    $message_wa = '-TGMPrint- %20Haii%20' . $dt->konsumen_nama . '%20Terima%20Kasih%20Sudah%20Berbelanja%20di%20TGMPrint%20Rp.%20' . number_format($real_total) . '%20Dengan%20Nomor%20SPK%20 :' . $dt->nospk . '%20Untuk%20Cek%20Progress%20Pekerjaan%20Anda,%20Silahkan%20Cek%20Link%20dibawah%20ini%20:%20https://erp.tgmprint.com/cekpesanan/spk/' . urlencode(encrypt_spk($dt->nospk));
                                    ?>
                                    <tr>
                                        <td><?= $dt->nospk ?></td>
                                        <td>
                                            <?= date_indo($dt->created_at) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($dt->created_at)) ?></small>
                                        </td>
                                        <td>
                                            Nama: <?= $dt->konsumen_nama ?><br>
                                            WA: <?= $dt->konsumen_nohp ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $dt->nama_jenis_transaksi ?><br>
                                            <?php if ($dt->id_jenis_transaksi == 2 && !empty($dt->marketplace_nama)): ?>
                                                <small class="text-muted"><?= $dt->marketplace_nama ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= $item ?></td>
                                        <td class="text-end"><?= number_format($real_total) ?></td>
                                        <td class="text-center"><?= $status_pembayaran ?></td>
                                        <td class="text-end"><?= number_format($total_bayar) ?></td>
                                        <td class="text-end"><?= number_format($kurang_bayar) ?></td>
                                        <td><?= $dt->karyawan_nama ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url($controller . "/cetak_invoice_ulang/" . $dt->nospk) ?>"
                                                class="btn btn-sm btn-dark" title="Cetak Invoice">
                                                <i class="fa fa-print"></i>
                                            </a>
                                            <a href="https://api.whatsapp.com/send?phone=<?= $dt->konsumen_nohp ?>&text=<?= $message_wa ?>"
                                                target="_blank" class="btn btn-sm btn-success" title="Kirim WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> <!-- .table-responsive -->

                </div>
            </div>
        </div>
    </div>
</div>
