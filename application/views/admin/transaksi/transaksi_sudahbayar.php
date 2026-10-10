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
                        <table id="datatable-transaksi-sudahbayar" class="table table-bordered table-striped">
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
                            </tbody>
                        </table>
                    </div> <!-- .table-responsive -->

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#datatable-transaksi-sudahbayar').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "<?= base_url('transaksi/ajax_transaksi_sudahbayar') ?>",
                "type": "POST"
            },
            "columnDefs": [
                { 
                    "targets": [10], 
                    "orderable": false, 
                },
                {
                    "targets": [4, 5, 7, 8],
                    "className": "text-end"
                },
                {
                    "targets": [3, 6, 10],
                    "className": "text-center"
                }
            ],
        });
    });
</script>
