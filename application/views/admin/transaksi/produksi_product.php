<div class="container-fluid">
    <section class="card shadow-lg">
        <?php foreach ($tampilData as $dt): ?>
        <div class="card-body">
            <h5 class="card-title mb-4 text-center">
                <i class="bi bi-gear-fill me-2"></i>Proses Produksi - <?= $dt->routing_name ?> - Urutan Routing - <?= $dt->routing_urutan ?>
            </h5>

            <!-- Display BOM Data -->
            <div class="mb-4">
                <h6 class="fw-bold">Data Built Of Material (BOM) untuk produk ini:</h6>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nama Bahan</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bom as $b): ?>  
                            <tr>
                                <td><?= $b->bahan_nama ?></td>
                                <td><?= $b->quantity ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <hr>
            <!-- Form for Produksi Process -->
            <form action="<?= base_url('produksi/proses') ?>" method="post">
                
                <input type="hidden" name="produksi_id" value="<?= $dt->produksi_id ?>">
                <input type="hidden" name="routing_urutan" value="<?= $dt->routing_urutan ?>">
               

                <div class="row">
                    <!-- Left Side - Product and Material -->
                    <div class="col-md-6 mb-4">
                        <div class="mb-3 row">
                            <!-- Kolom pertama untuk No SPK -->
                            <div class="col-md-6">
                                <label for="nospk_input" class="form-label fw-bold">No SPK</label>
                                <input type="text" name="nospk" value="<?= $dt->nospk ?>" class="form-control" id="nospk_input" readonly>
                            </div>

                            <!-- Kolom kedua untuk Nama Konsumen -->
                            <div class="col-md-6">
                                <label for="konsumen_nama" class="form-label fw-bold">Nama Konsumen</label>
                                <input type="text" class="form-control" value="<?= $dt->konsumen_nama ?? '-' ?>" id="konsumen_nama" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="currentRouting" class="form-label fw-bold">Step Sekarang</label>
                            <?php 
                                $routing = $this->M_master->getRoutingproduct($dt->produksi_id)->result();
                                $currentRouting = 'Tidak ada routing';
                                if ($routing) {
                                    foreach ($routing as $route) {
                                        if ($route->routing_urutan == $dt->routing_urutan) {
                                            $currentRouting = $route->routing_name; ?>
                                             <input type="hidden" name="routing_id" value="<?= $route->routing_id ?>">
                                            <?php
                                             break;
                                        }
                                    }
                                }
                            ?>
                            <input type="text" class="form-control" value="<?= $currentRouting ?>" id="currentRouting" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="product_nama" class="form-label fw-bold">Produk Yang Dipesan</label>
                            <input type="text" class="form-control" value="<?= $dt->product_nama ?>" id="product_nama" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="bahanbaku_id" class="form-label fw-bold">Bahan Utama</label>
                            <select name="bahanbaku_id" id="bahanbaku_id" class="form-select" required>
                                <?php foreach ($bahanBaku as $bahan): ?>
                                    <option value="<?= $bahan->bahanbaku_id ?>" <?= ($bahan->bahanbaku_id == $dt->bahanbaku_id) ? 'selected' : '' ?>><?= $bahan->bahanbaku_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih bahan utama yang digunakan untuk produksi dan jika ada perubahan bahan.</div>
                        </div>

                       <div class="mb-3">
                            <label for="qty_pesanan" class="form-label fw-bold">Jumlah Pesanan</label>
                            <div class="row">
                                <!-- First input field for quantity -->
                                <div class="col-md-5">
                                    <input type="text" class="form-control" value="<?= $dt->qty ?>" id="qty_pesanan" readonly>
                                    <?= $dt->satuan_nama ?> 
                                </div>   
                                <div class="col-md-1 text-center">
                                    x
                                </div>
                                 <?php foreach ($bom as $b): ?>  
                                <div class="col-md-6">
                                    <input type="text" class="form-control" value="<?= $b->quantity ?>" id="quantity" readonly>
                                    <?= $b->satuan_nama ?> 
                                </div>
                                <?php endforeach; ?>

                                
                                <?php 
                                    $totalbahan = $dt->qty * $b->quantity;
                                    echo '<p class="text-danger">Total Bahan Baku yang digunakan untuk produksi ini adalah <b>' . $totalbahan . ' ' . $b->satuan_nama . '</b></p>';
                                ?>
                                
                            </div>
                        </div>
                               
                        
                                                 <?php 
                             if ($dt->urutan_routing == '2') {
                                 $memiliki_dimensi = 'checked';
                             } else {
                                 $memiliki_dimensi = '';
                             }
                         ?>
                         <div class="mb-3">
                             <div class="form-check">
                                 <input type="checkbox" name="memiliki_dimensi" id="memiliki_dimensi" <?= $memiliki_dimensi ?> value="1" onchange="toggleDimensi()" class="form-check-input">
                                 <label for="memiliki_dimensi" class="form-check-label fw-bold">Memiliki Dimensi</label>
                             </div>
                         </div>

                        <div id="dimensi_container" class="<?= ($dt->panjang !== '0.00' || $dt->lebar !== '0.00') ? '' : 'd-none' ?>">
                            <?php if ($dt->panjang !== '0.00' || $dt->lebar !== '0.00'): ?>
                                <div class="mb-3">
                                    <label for="panjang_produksi_input" class="form-label fw-bold">Panjang (meter)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?= $dt->panjang ?>" readonly>
                                        <input type="text" name="panjang_produksi" id="panjang_produksi_input" class="form-control" placeholder="Masukkan Panjang" value="<?= $dt->panjang == '0.00' ? '0' : '' ?>" <?= $dt->panjang == '0.00' ? '' : 'required' ?>>
                                        <span class="input-group-text">meter</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="lebar_produksi_input" class="form-label fw-bold">Lebar (meter)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?= $dt->lebar ?>" readonly>
                                        <input type="text" name="lebar_produksi" id="lebar_produksi_input" class="form-control" placeholder="Masukkan Lebar" value="<?= $dt->lebar == '0.00' ? '0' : '' ?>" <?= $dt->lebar == '0.00' ? '' : 'required' ?>>
                                        <span class="input-group-text">meter</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Hidden inputs for panjang and lebar when both are 0 -->
                                <input type="hidden" name="panjang_produksi" value="0">
                                <input type="hidden" name="lebar_produksi" value="0">
                            <?php endif; ?>
                        </div>
                       
                        
                    </div>

                    <?php
                        if ($dt->panjang !== '0.00' || $dt->lebar !== '0.00') {
                            $jumlah_produksi = $dt->qty * $b->quantity * $dt->panjang * $dt->lebar;
                        } else {
                            $jumlah_produksi = $dt->qty * $b->quantity;
                        }

                    ?>

                    <!-- Right Side - Status, Konsumen, Operator -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="qty_produksi_input" class="form-label fw-bold">Jumlah Produksi (<?= $b->satuan_nama ?>)</label>
                            <input type="text" class="form-control" name="qty_produksi" required value="<?= $jumlah_produksi ?>" id="qty_produksi_input">
                        </div>

                        <div class="mb-3">
                            <label for="qty_missing_input" class="form-label fw-bold">Jumlah Gagal Bahan (<?= $b->satuan_nama ?>)</label>
                            <input type="text" class="form-control" name="qty_missing" id="qty_missing_input" required>
                        </div>

                        <div class="mb-3">
                            <label for="alasan_missing" class="form-label fw-bold">Keterangan / Alasan Gagal Print</label>
                            <textarea name="alasan_missing" id="alasan_missing" class="form-control">
                                Tidak Ada Gagal Produksi
                            </textarea>
                        </div>
                        <div class="mb-3">
                            <label for="mesin_id" class="form-label fw-bold">Mesin yang Digunakan</label>
                            <select name="mesin_id" id="mesin_id" class="form-select" required>
                                <option value="100">-- Proses Manual --</option>
                                <?php foreach ($mesin as $m): ?>
                                    <option value="<?= $m->mesin_id ?>" <?= $dt->mesin_id == $m->mesin_id ? 'selected' : '' ?>>
                                        <?= $m->mesin_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih mesin yang akan digunakan untuk proses produksi</div>
                        </div>

                        <div class="mb-3">
                            <label for="lokasi_cetak" class="form-label fw-bold">Lokasi Cetak / Produksi & Pengurangan Stok</label>
                            <select name="lokasi_cetak" id="lokasi_cetak" class="form-select" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php foreach ($lokasi_cetak as $l): ?>
                                    <option value="<?= $l->gudang_id ?>" <?= $dt->lokasi_cetak == $l->gudang_id ? 'selected' : '' ?>>
                                        <?= $l->gudang_lokasi ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih lokasi cetak yang akan digunakan untuk proses produksi</div>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100" onclick="return window.produksiCalculator.validateForm()">
                            <i class="bi bi-save me-1"></i> Proses
                        </button>
                    </div>
                     
                </div>

               
                   
                    
            </form>
        </div>
        <?php endforeach; ?>
    </section>
</div>

<!-- Include Production Calculation JavaScript -->
<script src="<?= base_url('assets/js/produksi_calculation.js') ?>"></script>

<!-- Toggle Dimensi Function -->
<script>
function toggleDimensi() {
    const checkbox = document.getElementById('memiliki_dimensi');
    const dimensiContainer = document.getElementById('dimensi_container');
    const panjangInput = document.getElementById('panjang_produksi_input');
    const lebarInput = document.getElementById('lebar_produksi_input');
    
    if (checkbox.checked) {
        // Show dimensi container
        dimensiContainer.classList.remove('d-none');
        
        // Make inputs required if they exist
        if (panjangInput) {
            panjangInput.required = true;
        }
        if (lebarInput) {
            lebarInput.required = true;
        }
        
        console.log('Dimensi container shown');
    } else {
        // Hide dimensi container
        dimensiContainer.classList.add('d-none');
        
        // Remove required attribute and clear values
        if (panjangInput) {
            panjangInput.required = false;
            panjangInput.value = '0';
        }
        if (lebarInput) {
            lebarInput.required = false;
            lebarInput.value = '0';
        }
        
        console.log('Dimensi container hidden');
    }
    
    // Recalculate production quantity
    if (window.produksiCalculator) {
        window.produksiCalculator.calculateTotal();
    }
}

// Initialize toggle on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('memiliki_dimensi');
    if (checkbox) {
        // Set initial state based on checkbox
        if (checkbox.checked) {
            document.getElementById('dimensi_container').classList.remove('d-none');
        } else {
            document.getElementById('dimensi_container').classList.add('d-none');
        }
    }
});
</script>
