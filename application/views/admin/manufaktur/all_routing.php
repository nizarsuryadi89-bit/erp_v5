<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="panel-title"><?= $title ?></h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <p>Jumlah Data: <?= $total_rows ?></p>
                    </div>
                    <div class="col-sm-6 text-end">
                        <a href="<?= base_url('manufaktur') ?>" class="btn btn-primary">Beranda</a>
                        <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatable-detail">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nama Produk / Barang</th>
                                <th width="10%">Jenis Routing</th>
                                <th width="15%">Detail Variasi</th>
                                <th width="15%">Nama Routing</th>
                                <th width="15%">Material BOM</th>
                                <th width="5%">Urutan</th>
                                <th width="15%">Operator</th>
                                <th width="5%">Estimasi Waktu</th>
                                <th width="10%">Biaya Tenaga Kerja</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tampilData)): ?>
                                <?php
                                $no = 1;
                                foreach ($tampilData as $dt):
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $dt->product_nama ?></td>
                                        <td>
                                            <?php if ($dt->jenis_routing == 'Global'): ?>
                                                <span class="badge bg-primary">Global</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Variasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= $dt->display_variasi ?></div>
                                            <?php if (!empty($dt->metadata)): ?>
                                                <?php 
                                                    $meta = json_decode($dt->metadata, true);
                                                    if (is_array($meta) && !empty($meta)): 
                                                ?>
                                                    <div class="mt-1">
                                                        <?php foreach ($meta as $key => $val): ?>
                                                            <?php if ($val !== '' && $val !== null): ?>
                                                                <span class="badge bg-light text-secondary border fw-normal mb-1 me-1" style="font-size: 0.75rem;">
                                                                    <?= ucwords(str_replace('_', ' ', $key)) ?>: <span class="text-dark fw-bold"><?= $val ?></span>
                                                                </span>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= $dt->routing_name ?></strong></td>
                                        <td>
                                            <?php if (!empty($dt->bom_material_name)): ?>
                                                <?= $dt->bom_material_name ?><?= !empty($dt->bom_varian) ? ' - ' . $dt->bom_varian : '' ?>
                                            <?php else: ?>
                                                <em class="text-muted">Tanpa Material</em>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $dt->routing_urutan ?></td>
                                        <td><?= $dt->operator_nama ?></td>
                                        <td><?= $dt->est_time ?></td>
                                        <td><?= number_format($dt->labor_cost, 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('manufaktur/routing/' . $dt->product_id) ?>"
                                                class="btn btn-info btn-sm" title="Lihat Routing Produk">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Tidak ada data routing
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 p-3 bg-light rounded border">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Keterangan Jenis Routing:</h6>
                    <ul class="text-muted small mb-0" style="list-style-type: none; padding-left: 0;">
                        <li class="mb-1"><span class="badge bg-primary me-2" style="width: 60px;">Global</span> : Proses produksi atau routing ini berlaku umum untuk <strong>semua variasi</strong> dari produk tersebut.</li>
                        <li><span class="badge bg-success me-2" style="width: 60px;">Variasi</span> : Proses produksi atau routing ini bersifat khusus dan hanya berlaku untuk <strong>variasi tertentu</strong> saja sesuai yang tertera pada kolom Detail Variasi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Initialize Datatable if datatable-detail is used -->
<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#datatable-detail')) {
            $('#datatable-detail').DataTable().destroy();
        }
        $('#datatable-detail').DataTable({
            "pageLength": 25,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "drawCallback": function (settings) {
                var api = this.api();
                var rows = api.rows({page: 'current'}).nodes();
                
                var lastProduct = null;
                var lastVariasi = null;
                
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    var productCell = $(row).find('td:eq(1)');
                    var jenisCell = $(row).find('td:eq(2)');
                    var variasiCell = $(row).find('td:eq(3)');
                    
                    var productText = productCell.text().trim();
                    var variasiText = variasiCell.text().trim();
                    
                    var currentProduct = productText;
                    var currentVariasi = productText + '|' + variasiText;
                    
                    // Grup untuk Nama Produk & Jenis Routing
                    if (currentProduct !== lastProduct) {
                        productCell.css('color', '');
                        jenisCell.css('visibility', 'visible');
                        lastProduct = currentProduct;
                    } else {
                        // Sembunyikan jika sama dengan baris sebelumnya
                        productCell.css('color', 'transparent');
                        jenisCell.css('visibility', 'hidden');
                    }
                    
                    // Grup untuk Detail Variasi
                    if (currentVariasi !== lastVariasi) {
                        variasiCell.css('color', '');
                        lastVariasi = currentVariasi;
                    } else {
                        variasiCell.css('color', 'transparent');
                    }
                }
            }
        });
    });
</script>