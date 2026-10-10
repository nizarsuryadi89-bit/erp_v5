<style>
    .detail-stock-card {
        background: var(--primary-gradient);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        text-align: center;
        margin-bottom: 2rem;
    }
    .detail-stock-value {
        font-size: 5rem;
        font-weight: 900;
        line-height: 1;
        margin: 1rem 0;
        text-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .detail-unit-label {
        font-size: 1.2rem;
        font-weight: bold;
        opacity: 0.9;
        letter-spacing: 2px;
    }
</style>

<!-- Modal Detail Inventory -->
<div class="modal fade" id="modalInventoryDetail" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h1 class="fw-900 text-primary mb-0" id="det_material_variant" style="font-size: 2.5rem; letter-spacing: -1.5px; line-height: 1.1;">Varian Produk</h1>
                    <div class="text-dark fw-bold opacity-75 mt-1" id="det_material_name" style="font-size: 1.2rem; letter-spacing: 0.5px;">Nama Material</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="detail-stock-card shadow-lg">
                            <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold mb-2">SALDO SAAT INI</span>
                            <div class="detail-stock-value" id="det_qty_large">0</div>
                            <div class="detail-unit-label" id="det_unit_name">SATUAN</div>
                            <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-center gap-4">
                                <div class="text-center">
                                    <div class="small opacity-75 fw-bold">KODE BARCODE</div>
                                    <div class="fw-900 font-monospace" id="det_barcode_label">-</div>
                                </div>
                                <div class="text-center">
                                    <div class="small opacity-75 fw-bold">LOKASI RAK</div>
                                    <div class="fw-900 text-uppercase" id="det_rak_label">-</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 bg-light rounded-4 p-3 mb-4">
                             <div class="fw-900 text-dark small mb-2"><i class="fa fa-info-circle me-1"></i> CATATAN TERAKHIR</div>
                             <p class="text-muted small mb-0 fst-italic" id="det_last_note">-</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-7">
                        <h5 class="fw-900 text-dark mb-4 px-2"><i class="fa fa-history me-2 text-primary"></i>RIWAYAT AKTIVITAS TERAKHIR</h5>
                        <div class="ps-3">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle" style="font-size: 0.85rem;">
                                    <thead class="text-muted fw-bold border-bottom">
                                        <tr>
                                            <th>TANGGAL</th>
                                            <th>TIPE</th>
                                            <th class="text-end">JUMLAH</th>
                                            <th>USER</th>
                                        </tr>
                                    </thead>
                                    <tbody id="det_history_body">
                                        <!-- Data via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light btn-premium rounded-pill px-5 border" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showMaterialDetailModal(barcode, warehouse_id) {
        if (!barcode) return;
        $.ajax({
            url: '<?= base_url('warehouse/ajax_get_inventory_detail') ?>',
            type: 'POST',
            data: {
                barcode: barcode,
                warehouse_id: warehouse_id || ''
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var mat = res.material;
                    var history = res.history;

                    $('#det_material_name').text(mat.bahan_nama);
                    $('#det_material_variant').text(mat.varian);
                    $('#det_barcode_label').text(mat.barcode);
                    $('#det_rak_label').text(mat.rak_kode || 'BELUM DIATUR');

                    var konversi = parseFloat(mat.konversi || 1);
                    var base_qty = parseFloat(mat.qty);
                    var main_qty = Math.floor(base_qty / konversi);
                    var small_qty = Math.round(base_qty % konversi);

                    $('#det_qty_large').text(main_qty.toLocaleString());
                    $('#det_unit_name').text(mat.satuan_nama + (small_qty > 0 ? ' + ' + small_qty + ' ' + (mat.satuan_produksi || 'Lbr') : ''));

                    var h_html = '';
                    if (history && history.length > 0) {
                        history.forEach(function(h) {
                            var type_badge = '';
                            if (h.trx_type == 'Pembelian') type_badge = 'bg-soft-success text-success';
                            else if (h.trx_type == 'Mutation' || h.trx_type == 'Dispatch') type_badge = 'bg-soft-blue text-primary';
                            else if (h.trx_type == 'Opname') type_badge = 'bg-soft-warning text-warning';
                            else type_badge = 'bg-light text-muted';

                            var qty_display = (parseFloat(h.qty_in) > 0 ? '+' : '-') + (parseFloat(h.qty_in) || parseFloat(h.qty_out)).toLocaleString();

                            h_html += `
                                <tr class="border-bottom border-light">
                                    <td><span class="text-muted fw-bold">${h.created_at}</span></td>
                                    <td><span class="badge ${type_badge} rounded-pill px-3">${h.trx_type}</span></td>
                                    <td class="text-end fw-900">${qty_display}</td>
                                    <td class="small">${h.user_nama || 'System'}</td>
                                </tr>
                            `;
                        });
                        $('#det_last_note').text(history[0].catatan || '-');
                    } else {
                        h_html = '<tr><td colspan="4" class="text-center text-muted p-4">Belum ada riwayat transaksi</td></tr>';
                        $('#det_last_note').text('-');
                    }
                    $('#det_history_body').html(h_html);

                    $('#modalInventoryDetail').modal('show');
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ditemukan',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });
    }
</script>
