<style>
    .form-group label {
        font-weight: bold;
    }
    .select2-container .select2-selection--single {
        height: 45px !important;
        padding: 10px;
        font-size: 12px;
    }
</style>
<script type="text/javascript">
    function showDiv(select){
        if(select.value==2){
            document.getElementById('hidden_div').style.display = "block";
            document.getElementById('email_div').style.display = "none";
        } else if (select.value==3) {
            document.getElementById('email_div').style.display = "block";
            document.getElementById('hidden_div').style.display = "none";
        }else{
            document.getElementById('hidden_div').style.display = "none";
            document.getElementById('email_div').style.display = "none";
        }
    } 
</script>

<div class="row p-4">
    <div class="card">
        <div class="card-body">
            <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                <form action="<?= base_url('transaksi/step_satu') ?>" method="post">
                    <div class="row">
                        <div class="col-md-9 col-xs-12">
                            <!-- Konsumen -->
                            <div class="form-group">
                                <label for="konsumenSelect">Pilih Konsumen</label>
                                <select name="konsumen_id" id="konsumenSelect1" class="form-control select2" style="width:100%;">
                                    <?php 
                                        foreach($konsumen as $kons){
                                    ?>  
                                    <option value="<?= $kons->konsumen_id ?>"><?= $kons->konsumen_nama ?></option>

                                    <?php 
                                        }
                                    ?>
                                </select>
                                <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
                            </div>

                            <!-- Jenis Transaksi -->
                            <div class="form-group">
                                <label for="id_jenis_transaksi">Jenis Transaksi</label>
                                <select name="id_jenis_transaksi" id="id_jenis_transaksi" class="form-control form-control-lg" onchange="showDiv(this)">
                                    <option value="1">On The Spot</option>
                                    <option value="2">Marketplace</option>
                                    <option value="3">WhatsApp / Email</option>
                                </select>
                            </div>

                            <!-- Marketplace (tampil jika dipilih) -->
                            <div id="hidden_div" style="display:none;">
                                <div class="form-group">
                                    <label for="id_marketplace">Marketplace</label>
                                    <select name="id_marketplace" id="id_marketplace" class="form-control form-control-lg">
                                        <option value="0">Onsite</option>
                                        <option value="1">Tokopedia</option>
                                        <option value="2">Shopee</option>
                                        <option value="3">Website</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="resi_marketplace">No Resi Marketplace</label>
                                    <input type="text" class="form-control form-control-lg" name="resi_marketplace" value="-" placeholder="Masukkan No Resi">
                                </div>
                            </div>

                            <!-- Email/NoHP (tampil jika dipilih) -->
                            <div id="email_div" style="display:none;">
                                <div class="form-group">
                                    <label for="email_nohp">Email / No. HP</label>
                                    <input type="text" class="form-control form-control-lg" name="email_nohp" placeholder="Masukkan Email / No HP">
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button class="btn btn-dark" type="submit" name="step_satu">
                                    <i class="fa fa-plus"></i> Buat Pesanan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#konsumenSelect1').select2({
        placeholder: 'Pilih Konsumen...',
        allowClear: true, // opsional
        width: '100%'     // agar konsisten dengan CSS
    });
});
</script>


<script>
    $(document).ready(function () {
        $('#konsumenSelect').select2({
            placeholder: 'Cari Konsumen...',
            width: '100%',
            ajax: {
                url: '<?= base_url("transaksi/search_konsumen") ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search keyword
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.konsumen_id,
                                text: item.konsumen_nama
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });
</script>