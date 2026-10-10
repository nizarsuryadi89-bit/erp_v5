<script type="text/javascript">
    function showDiv(select) {
        if (select.value == 2) {
            document.getElementById('hidden_div').style.display = "block";
            document.getElementById('email_div').style.display = "none";
        } else if (select.value == 3) {
            document.getElementById('email_div').style.display = "block";
            document.getElementById('hidden_div').style.display = "none";
        } else {
            document.getElementById('hidden_div').style.display = "none";
            document.getElementById('email_div').style.display = "none";
        }
    } 
</script>

<div class="row">
    <!-- <section class="panel"> -->
    <div class="panel-body">
        <div id="small-dialog" class="dialog dialog-sm zoom-anim-dialog mfp-hide">
            <!-- <h1>Transaksi</h1> -->
            <p>Buat Transaksi Baru dan Pending yang lama</p>
            <a href="<?= base_url('transaksi/add/1') ?>" class="btn btn-warning">Trans 1</a>
            <a href="<?= base_url('transaksi/add/2') ?>" class="btn btn-primary">Trans 2</a>
            <a href="<?= base_url('transaksi/add/3') ?>" class="btn btn-danger">Trans 3</a>
            <a href="<?= base_url('transaksi/add/4') ?>" class="btn btn-dark">Trans 4</a>
        </div>
        <div class="col-md-2">
            <a class="mt-xs mb-xs mr-xs popup-with-zoom-anim btn btn-default btn-block" href="#small-dialog"> Transaksi
                Baru /Pending Transaksi</a>
            <?php
            foreach ($kategori as $kat) {
                ?>
                <a class="mb-xs mt-xs mr-xs btn btn-<?= $kat->kategori_img ?> btn-block"
                    href="<?= base_url('transaksi/add/' . $transaksi . '/' . $kat->kategori_id) ?>"><?= $kat->kategori_nama ?></a>
                <?php
            }
            ?>

        </div>

        <div class="col-md-10">
            <?php
            switch ($this->uri->segment(4)) {
                case '1':
                    $this->load->view('admin/transaksi/product/product_digital');
                    break;
                case '2':
                    $this->load->view('admin/transaksi/product/product_large_format');
                    break;
                case '3':
                    $this->load->view('admin/transaksi/product/product_merchandise');
                    break;
                case '4':
                    $this->load->view('admin/transaksi/product/product_custom_print');
                    break;
                default:
                    $this->load->view('admin/transaksi/product/product_digital');

            }
            ?>

        </div>

        <div class="col-md-12">

            <table class="table table-bordered" id="transaksi">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Daftar Product</th>
                        <th width="15%">Keterangan</th>
                        <th width="10%">Harga Satuan</th>
                        <th width="10%">Qty</th>
                        <th width="10%">P x L</th>
                        <th width="10%">Harga Total</th>
                        <th width="5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if ($jmlTmp != '0') {
                        foreach (@$tampilTmp as $dt) {
                            $cekkonsumen = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $dt->konsumen_id])->row_array();
                            if ($dt->harga_aktif = '1') {
                                $hargaSatuan = $dt->harga_1;
                            } elseif ($dt->harga_aktif = '2') {
                                $hargaSatuan = $dt->harga_2;
                            } elseif ($dt->harga_aktif = '3') {
                                $hargaSatuan = $dt->harga_3;
                            }
                            ?>
                            <tr class="del_mem<?php echo $dt->temp_id ?> ">
                                <td><?= $no ?></td>
                                <td>


                                    <?php
                                    if ($dt->detail_product <> '') {
                                        ?>
                                        <p><?= $dt->detail_product ?> <br>
                                        <?php
                                    } else {
                                        echo "<b>Detail Product Belum di Set Oleh Admin</b>";
                                        echo "<br>Hub Admin atau delete data ini";
                                        ?>
                                            <button type="button" class="btn_del" id="<?php echo $dt->temp_id ?>">Hapus</button>

                                            <?php
                                            echo "<b><br> Operator Tidak mengetahui Bahan yang akan di cetak</b>";
                                    }
                                    ?>


                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($dt->finishing_id == 1) {
                                        ?>
                                        <span class="badge">+ Finishing</span>
                                        <?php
                                    }
                                    ?>
                                    <br>
                                    <br>
                                    Ket : <?= $dt->keterangan ?>
                                </td>
                                <td class="text-right">

                                    <?= number_format($hargaSatuan) ?>

                                </td>
                                <td class="text-center">
                                    <?= $dt->temp_qty ?>


                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($dt->temp_panjang <> '0.0' && $dt->temp_lebar <> '0.0') {
                                        ?>
                                        <?= $dt->temp_panjang ?> x <?= $dt->temp_lebar ?>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    if ($dt->temp_panjang <> '0.0' && $dt->temp_lebar <> '0.0') {
                                        $totalHarga = $hargaSatuan * $dt->temp_qty * $dt->temp_panjang * $dt->temp_lebar;
                                    } else {
                                        $totalHarga = $hargaSatuan * $dt->temp_qty;
                                    }
                                    ?>
                                    <b><?= number_format($totalHarga) ?></b>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-round btn_del"
                                        id="<?php echo $dt->temp_id ?>"><i class="fa fa-remove"></i></button>
                                </td>
                            </tr>
                            <?php
                            $no++;

                            $totaltrans = $totaltrans + $totalHarga;
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'><b>Belum Ada Data Yang Tersedia</b></td></tr>";

                    }

                    ?>
                    <tr>
                        <td colspan="6">
                            <p class="text-danger">Jika menambahkan Produk yang dipesan, pilih kembali Nama Konsumen</p>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        <div class="col-md-12">
            <br>
        </div>
        <div class="col-md-4 text-left">
            <div class="form-group ">
                <label class="col-md-3 control-label" for="inputDisabled">Konsumen </label>
                <div class="col-md-9">
                    <form action="" method="post">
                        <select data-plugin-selectTwo name="konsumen_id" id="konsumen_id" class="form-control"
                            onchange="this.form.submit()">
                            <option value="">-- Pilih Konsumen --</option>
                            <?php foreach ($konsumen as $b): ?>
                                <?php
                                if ($b->konsumen_id == $getkonsumen['konsumen_id']) {
                                    ?>
                                    <option value="<?= $b->konsumen_id ?>" selected><?= $b->konsumen_nohp ?></option>
                                <?php
                                } else {
                                    $selected = '';
                                }
                                ?>
                                <option value="<?= $b->konsumen_id ?>"><?= $b->konsumen_nohp ?> - <?= $b->konsumen_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <a class="modal-with-form btn btn-warning btn-block text-center" href="#add_konsumen"> Add New </a>
                </div>

            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="inputDisabled">Nama </label>
                <div class="col-md-9">
                    <p><?= @$dataTmp['konsumen_nama'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="inputDisabled">No HP </label>
                <div class="col-md-9">
                    <?= @$dataTmp['konsumen_nohp'] ?>
                </div>
            </div>
            <form action="<?= site_url('transaksi/add_pemesanan') ?>" method="post">
        </div>
        <div class="col-md-4">
            <div class="form-group text-center">
                <!-- <label class="col-md-12 control-label" for="inputDisabled"> <b>Metode Pemesanan</b> </label> -->
                <div class="col-md-12">
                    <input type="hidden" class="form-control" name="kode" value="<?= @$kode ?>" readonly>
                    <input type="hidden" class="form-control" name="konsumen_id" value="<?= @$dataTmp['konsumen_id'] ?>"
                        readonly>
                    <select name="id_jenis_transaksi" id="id_jenis_transaksi" class="form-control"
                        onchange="showDiv(this)">
                        <option value="1">On The Spot</option>
                        <option value="2">Marketplace</option>
                        <option value="3">WhatsApp / Email</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div id="hidden_div" style="display:none;">
                    <label class="col-md-3 control-label" for="inputDisabled">No Resi </label>
                    <div class="col-md-9">
                        <select name="marketplace_id" id="marketplace_id" class="form-control">
                            <option value="0">Onsite</option>
                            <option value="1">Tokopedia</option>
                            <option value="2">Shopee</option>
                            <option value="3">Website</option>
                        </select>
                        <input type="text" class="form-control" name="resi_marketplace" value="-"
                            placeholder="Masukkan No Resi Marketplace">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div id="email_div" style="display:none;">
                    <label class="col-md-3 control-label" for="inputDisabled">Email </label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="email_wa" placeholder="Masukkan no WA Pemesan">
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-4">

            <div class="col-md-12">
                <div class="input-group mb-md">
                    <span class="input-group-addon">Total</span>
                    <input type="text" class="form-control" name="total_pemesanan" value="<?= $totaltrans ?>" readonly>
                </div>
            </div>


            <div class="col-md-9 col-xs-12">
                <input type="hidden" class="form-control" name="nospk" value="<?= $noSPK ?>">
                <input type="hidden" class="form-control" name="transaksi_tgl" value="<?= $hariini ?>">
                <input type="hidden" class="form-control" name="transaksi_jam" value="<?= $jamini ?>">
                <input type="text" class="form-control" name="deskprint_id" value="<?= $deskprint_id ?>">

                <div class="input-group mb-md">
                    <span class="input-group-addon">No SPK</span>
                    <input type="text" id="text-copy" class="form-control" name="spk" value="<?= $noSPK ?>" readonly>
                </div>
            </div>
            <div class="col-md-2 col-xs-12">
                <button class="btn btn-success btn-round copy-btn" type="button">
                    <span class="fa fa-copy"></span>
                </button>
            </div>



            <div class="form-group">
                <?php
                if (($this->input->post('konsumen_id')) || ($jmlTmp != 0)) {
                    ?>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary col-md-12"> <i class="fa fa-save"> </i> Kirim Ke
                            Kasir</button>
                    </div>
                <?php
                }
                ?>

            </div>
            </form>
        </div>
    </div>
</div>

<!-- </section> -->
</div>


<div id="add_konsumen" class="modal-block modal-block-primary mfp-hide">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Tambahkan Konsumen Baru </h2>
        </header>
        <div class="panel-body">
            <form class="form-horizontal form-bordered" method="post"
                action="<?= base_url('pengaturan/konsumen_add') ?>">
                <div class="form-group">
                    <label class="col-md-4 control-label" for="inputDisabled">Nama Konsumen</label>
                    <div class="col-md-8">
                        <input type="text" name="konsumen_nama" id="konsumen_nama" class="form-control" setfocus>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="inputDisabled">No HP</label>
                    <div class="col-md-8">
                        <input type="text" name="konsumen_nohp" id="konsumen_nohp" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="inputDisabled">Alamat</label>
                    <div class="col-md-8">
                        <input type="text" name="konsumen_alamat" id="konsumen_alamat" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="inputDisabled">Email</label>
                    <div class="col-md-8">
                        <input type="email" name="konsumen_email" id="konsumen_email" class="form-control">
                    </div>
                </div>
                <div class="form-group text-center">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button class="btn btn-default modal-dismiss">Cancel</button>
                    </div>
                </div>
            </form>
        </div>

    </section>
</div>

</div>





<script src="http://code.jquery.com/jquery-latest.js"></script>
<div class="modal fade" id="modal_confirm" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Peringatan</h3>
            </div>
            <div class="modal-body">
                <center>
                    <h4>Apakah anda benar-benar ingin menghapusnya?</h4>
                </center>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-success" id="btn_yes">Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- modal hapus -->
<script>
    $(document).ready(function () {
        $('.btn_del').on('click', function () {
            var kode = $(this).attr('id');
            $("#modal_confirm").modal('show');
            $('#btn_yes').attr('name', kode);
        });
        $('#btn_yes').on('click', function () {
            var id = $(this).attr('name');
            $.ajax({
                type: "POST",
                url: "<?= base_url('transaksi/deletebilling_act') ?>",
                data: {
                    temp_id: id
                },
                success: function () {
                    $("#modal_confirm").modal('hide');
                    $(".del_mem" + id).empty();
                    $(".del_mem" + id).html("<td colspan='6'><center>Deleting...</center></td>");
                    setTimeout(function () {
                        $(".del_mem" + id).fadeOut('slow');
                    }, 1000);
                }
            });
        });
    });

    var table = document.getElementById("transaksi"), sumHsl = 0;
    for (var t = 1; t < table.rows.length; t++) {
        sumHsl = sumHsl + parseInt(table.rows[t].cells[3].innerHTML);
    }
    document.getElementById("total").innerHTML = "Sum Value = " + sumHsl;

</script>