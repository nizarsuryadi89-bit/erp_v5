<div class="report-section-card">
    <div class="section-body">
        <?php if ($this->uri->segment(3) != NULL): ?>
            <!-- Selection Card for Selected Product -->
            <div id="product-selection-alert" class="mb-3">
                <?php if (isset($getproduk['product_nama'])): ?>
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0 px-3 py-2"
                        style="background: #e1f5fe; color: #01579b; border-radius: 8px;">
                        <i class="fas fa-check-circle me-2"></i>
                        <span class="small fw-bold text-uppercase">Produk Terpilih:</span>
                        <span class="ms-2 fw-bold" id="selected-product-name">
                            <?= $getproduk['product_nama'] ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ADD PRODUCT FORM -->
            <form method="post" id="form-add-temp">
                <input type="hidden" name="user_id" value="<?= $this->session->userdata('id') ?>">
                <input type="hidden" name="nospk" value="<?= $nospk ?>">
                <input type="hidden" name="product_id" id="hidden_product_id" value="<?= $getproduk['product_id'] ?? '' ?>">
                <input type="hidden" name="kategori_id" id="hidden_kategori_id"
                    value="<?= $getproduk['kategori_id'] ?? '' ?>">
                <input type="hidden" name="subkategori_id" id="hidden_subkategori_id"
                    value="<?= $getproduk['subkategori_id'] ?? '' ?>">

                <div class="row g-3">
                    <!-- BLOCK 1: PENCARIAN -->
                    <div class="col-md-4">
                        <div class="form-section p-3 h-100 position-relative shadow-sm rounded-4"
                            style="background: #f8fafc;">
                            <h6 class="mb-3"><i class="fas fa-search me-2 text-primary"></i>1. PILIH PRODUK</h6>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">CARI NAMA / SCAN
                                    BARCODE</label>
                                <select name="kodeproduk" id="productSelect" class="form-control select2"
                                    style="width:100%;">
                                    <option value="">-- Ketik Nama Produk --</option>
                                    <?php if (isset($getproduk['product_id'])): ?>
                                        <option value="<?= $getproduk['product_id'] ?>" selected>
                                            <?= $getproduk['product_nama'] ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- BLOCK 2: SPESIFIKASI (HARDCODED PREMIUM) -->
                    <div class="col-md-4">
                        <div class="form-section p-3 h-100 shadow-sm rounded-4" style="background: #f8fafc;">
                            <h6 class="mb-3 text-uppercase fw-bold" style="letter-spacing: 0.5px; color: #334155;">
                                <i class="fas fa-sliders-h me-2 text-primary"></i>2. SPESIFIKASI TEKNIS
                            </h6>
                            <!-- Kategori -->
                            <div class="row align-items-center mb-2">
                                <div class="col-sm-4">
                                    <label class="form-label small fw-bold text-muted mb-0">KATEGORI</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="kategori_id" id="kategori_id"
                                        class="form-select form-select-sm fw-bold border-0 bg-white" disabled>
                                        <option value="-">Pilih Kategori</option>
                                        <?php if (isset($kategori) && is_array($kategori)): ?>
                                            <?php foreach ($kategori as $kat): ?>
                                                <option value="<?= $kat->kategori_id ?>" <?= (isset($getproduk['kategori_id']) && $getproduk['kategori_id'] == $kat->kategori_id) ? 'selected' : '' ?>>
                                                    <?= $kat->kategori_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Jenis Produk (Subkategori) -->
                            <div class="row align-items-center mb-2">
                                <div class="col-sm-4">
                                    <label class="form-label small fw-bold text-muted mb-0">JENIS PRODUK</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="subkategori_id" id="subkategori_id"
                                        class="form-select form-select-sm fw-bold border-0 bg-white" disabled>
                                        <option value="">No Selected</option>
                                        <?php if (isset($subkategori) && is_array($subkategori)): ?>
                                            <?php foreach ($subkategori as $s): ?>
                                                <option value="<?= $s->subkategori_id ?>" <?= (isset($getproduk['subkategori_id']) && $getproduk['subkategori_id'] == $s->subkategori_id) ? 'selected' : '' ?>>
                                                    <?= $s->subkategori_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div id="dynamic-category-view" class="mt-2 border-top pt-2">
                                <?php if (isset($getproduk['kategori_id'])) {
                                    $this->load->view('admin/transaksi/createspk/kategori_dynamic', ['getproduk' => $getproduk]);
                                } ?>
                            </div>
                        </div>
                    </div>

                    <!-- BLOCK 3: RINCIAN -->
                    <div class="col-md-4">
                        <div class="form-section p-3 h-100 shadow-sm rounded-4" style="background: #f8fafc;">
                            <h6 class="mb-3"><i class="fas fa-list-ol me-2 text-primary"></i>3. RINCIAN PESANAN</h6>
                            <div class="row align-items-center mb-2">
                                <div class="col-sm-5"><label class="form-label small fw-bold text-muted mb-0">QTY
                                        (UNIT)</label></div>
                                <div class="col-sm-7"><input type="number" name="qty" id="qty"
                                        class="form-control form-control-sm fw-bold text-primary" placeholder="0" required>
                                </div>
                            </div>
                            <div class="row align-items-center mb-2">
                                <div class="col-sm-5"><label
                                        class="form-label small fw-bold text-muted mb-0">APPROVAL</label></div>
                                <div class="col-sm-7">
                                    <select name="approve" id="approve" class="form-select form-select-sm fw-bold">
                                        <option value="1">🚀 LANJUT</option>
                                        <option value="2">🧪 SAMPEL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-sm-5"><label class="form-label small fw-bold text-muted mb-0">LOKASI</label>
                                </div>
                                <div class="col-sm-7">
                                    <select name="lokasi_cetak" id="lokasi_cetak"
                                        class="form-select form-select-sm fw-bold">
                                        <option value="2" <?= (isset($perusahaan_id) && $perusahaan_id == 2) ? 'selected' : '' ?>>🏢 OFFICIAL</option>
                                        <option value="3" <?= (isset($perusahaan_id) && $perusahaan_id == 3) ? 'selected' : '' ?>>🛍️ MERCH</option>
                                    </select>
                                </div>
                            </div>
                            <!-- NEW FEATURE: LINKED ITEM (KAITKAN PRODUK) -->
                            <div class="mb-2 d-none" id="linked-item-container" style="display: none !important;">
                                <label class="form-label small fw-bold mb-1 text-uppercase" style="color: #db2777;">
                                    <i class="fas fa-link me-1"></i> Terapkan Pada (Target)
                                </label>
                                <select id="linked_item_select" class="form-select form-select-sm" style="border-color: #fbcfe8; background-color: #fdf2f8; color: #9d174d; font-weight: 700; font-size: 0.8rem;">
                                    <option value="">-- Tidak Dikaitkan --</option>
                                </select>
                                <div class="mt-1" style="font-size: 0.65rem; color: #be185d;">
                                    Pilih produk utama jika item ini adalah Jasa / Sablon tambahannya.
                                </div>
                            </div>
                            <!-- NEW FEATURE: KETERANGAN ITEM -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted mb-1 text-uppercase">Keterangan
                                    Khusus</label>
                                <textarea name="keterangan"
                                    class="form-control form-control-sm rounded-3 border-primary-subtle" rows="2"
                                    placeholder="Tulis catatan untuk tim produksi..."
                                    style="font-size: 0.8rem;"></textarea>
                                <div class="mt-1" style="font-size: 0.65rem; color: #0891b2;">
                                    <i class="fas fa-info-circle me-1"></i> Kini catatan langsung terbaca oleh tim produksi.
                                </div>
                            </div>
                            <!-- ESTIMASI HARGA LIVE -->
                            <div id="price-estimation-box" class="mb-3 d-none">
                                <div class="d-flex justify-content-between align-items-end p-3 rounded-3" style="background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border: 1px solid #bbf7d0;">
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Harga Satuan:</span>
                                        <div id="est-harga-satuan" class="fw-bold" style="color: #059669; font-size: 1rem; font-style: italic;">Rp 0</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">SUBTOTAL</span>
                                        <div id="est-subtotal" class="fw-bold" style="color: #047857; font-size: 1.3rem;">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                            <div id="price-estimation-warning" class="mb-3 d-none">
                                <div class="p-2 rounded-3 text-center small" style="background: #fff7ed; border: 1px solid #fed7aa; color: #c2410c;">
                                    <i class="fas fa-exclamation-triangle me-1"></i> <span id="est-warning-text">Harga belum dikonfigurasi</span>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn btn-premium btn-premium-success w-100 py-2 mt-2 shadow-sm font-weight-bold">
                                <i class="fas fa-cart-plus me-2"></i> TAMBAHKAN KE LIST
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>