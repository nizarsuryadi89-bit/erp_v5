<?php if ($this->uri->segment(3) != NULL): ?>
    <div class="report-section-card mt-4">
        <div class="section-header" style="background: var(--primary-gradient);">
            <h6 class="mb-0 text-white"><i class="fas fa-shopping-basket me-2"></i>ITEM DALAM INVOICE #
                <?= $nospk ?>
            </h6>
        </div>
        <div class="section-body p-0">
            <div class="row g-0">
                <div class="col-12 bg-light border-bottom p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0 small fw-bold text-muted text-uppercase mb-2"><i
                                    class="fas fa-info-circle me-1 text-primary"></i> ITEM YANG SUDAH DITAMBAHKAN</p>
                            <!-- Bulk Delete Action -->
                            <div id="bulkActionContainer" style="display: none;">
                                <button type="button"
                                    class="btn btn-sm btn-danger shadow-sm fw-bold px-3 animate__animated animate__fadeIn"
                                    id="btnHapusMassal">
                                    <i class="fas fa-trash-alt me-2"></i> HAPUS TERPILIH (<span id="countSelected">0</span>)
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-card p-3 shadow-sm border-0 bg-white">
                                <div class="row g-0 text-center">
                                    <div class="col-6 border-end">
                                        <span class="text-muted small fw-bold">TOTAL ITEM</span>
                                        <h4 class="mb-0 fw-bold text-primary" id="jmlItem">0</h4>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small fw-bold">TOTAL BIAYA</span>
                                        <h4 class="mb-0 fw-bold text-dark">Rp <span id="totalHarga">0</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="transaksi">
                            <thead>
                                <tr>
                                    <th width="3%" class="text-center px-0">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAllItem"
                                                style="cursor:pointer; width:1.2rem; height:1.2rem; border-width: 2px;">
                                        </div>
                                    </th>
                                    <th width="4%" class="text-center">#</th>
                                    <th width="20%">PRODUK / LAYANAN</th>
                                    <th width="15%">KETERANGAN</th>
                                    <th width="8%" class="text-center">QTY</th>
                                    <th width="10%" class="text-end">HARGA SATUAN</th>
                                    <th width="10%" class="text-center">UKURAN</th>
                                    <th width="15%" class="text-center">LOKASI & APPROVAL</th>
                                    <th width="10%" class="text-end">SUBTOTAL</th>
                                    <th width="8%" class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-footer bg-light p-4 border-top">
            <form action="<?= site_url('transaksi/add_pemesanan') ?>" method="post">
                <input type="hidden" name="nospk" value="<?= $nospk ?>">
                <input type="hidden" name="total_pemesanan" id="total_pemesanan">

                <!-- Hidden Required Data for Backend Insertion -->
                <input type="hidden" name="konsumen_id" value="<?= $getkosumen['konsumen_id'] ?? '' ?>">
                <input type="hidden" name="deskprint_id" value="<?= $this->session->userdata('id') ?>">
                <input type="hidden" name="id_jenis_transaksi" value="<?= $getkosumen['id_jenis_transaksi'] ?? '1' ?>">
                <input type="hidden" name="marketplace_id" value="<?= $getkosumen['marketplace_id'] ?? '0' ?>">
                <input type="hidden" name="resi_marketplace" value="<?= $getkosumen['resi_marketplace'] ?? '-' ?>">
                <input type="hidden" name="id_pengiriman" value="<?= $getkosumen['id_pengiriman'] ?? '0' ?>">
                <input type="hidden" name="batch_pengiriman" value="<?= $getkosumen['batch_pengiriman'] ?? '' ?>">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0"
                            style="background: <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? '#e8f5e9' : '#fff3e0' ?>; color: <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? '#2e7d32' : '#e65100' ?>;">
                            <i class="fas <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? 'fa-check-double' : 'fa-exclamation-triangle' ?> fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">KONFIRMASI AKHIR</h6>
                                <p class="mb-0 small">
                                    <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? 'Pesanan Marketplace akan langsung dikirim ke antrian <b>PRODUKSI</b>.' : 'Kirim semua item di atas ke kasir untuk proses pembayaran.' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <button type="button" id="btnKirimKasir"
                            class="btn btn-premium <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? 'btn-premium-success' : 'btn-premium-warning' ?> w-100 fs-5 py-3 shadow-sm" disabled>
                            <i class="fas fa-lock me-2"></i> ITEM KOSONG
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>