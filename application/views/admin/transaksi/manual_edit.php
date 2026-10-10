<!-- Premium Header -->
<?php $this->load->view('layout_admin/header_modul'); ?>

<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    :root {
        --tgm-primary: #4338ca;
        --tgm-success: #059669;
        --tgm-danger: #e11d48;
        --tgm-warning: #d97706;
        --tgm-bg: #f8fafc;
    }

    body {
        background-color: var(--tgm-bg);
    }

    .premium-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #edf2f7;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .checkout-header {
        background: white;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-input-card {
        background: #f1f5f9;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .table-cart thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-cart tbody td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .summary-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        position: sticky;
        top: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .total-display {
        font-family: 'JetBrains Mono', monospace;
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -0.05em;
        color: var(--tgm-primary);
        margin: 1rem 0;
    }

    .btn-action-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #fee2e2;
        color: #b91c1c;
        transition: all 0.2s;
    }

    .btn-action-circle:hover {
        transform: scale(1.1);
        background: #fca5a5;
    }

    .mono {
        font-family: 'JetBrains Mono', monospace;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        height: 45px !important;
        display: flex !important;
        align-items: center !important;
    }
</style>

<div class="app-content py-4">
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Left Side: Input & Cart -->
            <div class="col-lg-8">
                <div class="premium-card">
                    <div class="checkout-header">
                        <div>
                            <h4 class="fw-800 text-dark mb-0">EDIT MANIFEST: <?= $transaksi->nospk ?></h4>
                            <small class="text-muted">Perbarui data transaksi manual yang sudah tercatat</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('penjualan/manual_list') ?>"
                                class="btn btn-light rounded-pill px-4 fw-bold border">
                                <i class="fas fa-arrow-left me-2"></i> KEMBALI
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form id="transactionForm" action="<?= base_url('transaksi/manual_update') ?>" method="post">
                            <input type="hidden" name="nospk" value="<?= $transaksi->nospk ?>">
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">IDENTITAS KONSUMEN</label>
                                    <select id="konsumen_id" name="konsumen_id" class="form-select select2" required>
                                        <option value="<?= $transaksi->konsumen_id ?>" selected>
                                            <?= $transaksi->konsumen_nama ?> (<?= $transaksi->konsumen_nohp ?: '-' ?>)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">TANGGAL TRANSAKSI</label>
                                    <input type="date" id="tanggal_transaksi" name="tanggal_transaksi"
                                        class="form-control rounded-pill px-3" required 
                                        value="<?= date('Y-m-d', strtotime($transaksi->tgl_bayar)) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">SALES / PIC</label>
                                    <input type="text" name="sales_person" class="form-control rounded-pill px-3"
                                        placeholder="Nama Sales..." value="<?= $transaksi->sales_person ?? $this->session->userdata('username') ?>">
                                </div>
                            </div>

                            <div class="product-input-card">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold"><i class="fas fa-barcode me-1"></i>
                                            PRODUK / ITEM</label>
                                        <select id="product_id" class="form-select select2">
                                            <option value="">Cari Nama Produk...</option>
                                            <?php if (isset($produk)):
                                                foreach ($produk as $p): ?>
                                                    <option value="<?= $p->product_id ?>"
                                                        data-harga="<?= $p->harga_aktif ?? 0 ?>"><?= $p->product_nama ?>
                                                    </option>
                                                <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-center d-block">QUANTITY</label>
                                        <input type="number" id="jumlah"
                                            class="form-control rounded-3 text-center fw-bold" min="1" value="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">HARGA SATUAN (RP)</label>
                                        <input type="number" id="harga_satuan"
                                            class="form-control rounded-3 mono fw-bold text-primary" placeholder="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" id="addProductBtn"
                                            class="btn btn-dark w-100 rounded-3 py-2 shadow-sm">
                                            <i class="fas fa-plus-circle me-1"></i> TAMBAH
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive" id="cartSection">
                                <table class="table table-cart mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-0">Item Description</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-end">Line Total</th>
                                            <th class="text-center pe-0"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartBody"></tbody>
                                </table>
                            </div>

                            <div id="emptyCart" class="text-center py-5" style="display: none;">
                                <img src="https://cdn-icons-png.flaticon.com/512/11329/11329073.png"
                                    style="width: 80px; opacity: 0.3;" class="mb-3">
                                <p class="text-muted fw-bold">Keranjang belanja masih kosong</p>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary & Payment -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="alert alert-warning rounded-4 border-0 small mb-4">
                        <i class="fas fa-info-circle me-2"></i> <b>Mode Edit:</b> Perubahan total akan mempengaruhi saldo kas atau piutang.
                    </div>

                    <h5 class="fw-800 text-dark mb-4 border-bottom pb-3">SUMMARY</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small fw-bold">TOTAL ITEMS</span>
                        <span id="total_items" class="fw-800 text-dark">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small fw-bold">SUBTOTAL</span>
                        <span id="total_subtotal" class="mono fw-bold">Rp 0</span>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted fw-bold d-block mb-1">PAYABLE AMOUNT</small>
                        <div id="total_tagihan" class="total-display">Rp 0</div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label small fw-bold text-muted">PAYMENT METHOD</label>
                        <select id="metode_bayar" name="metode_bayar"
                            class="form-select rounded-3 py-2 fw-bold text-primary" required>
                            <option value="">Select Method...</option>
                            <option value="cash" <?= ($transaksi->metode == 'tunai' || $transaksi->metode == 'cash') ? 'selected' : '' ?>>💵 TUNAI / CASH</option>
                            <option value="transfer" <?= $transaksi->metode == 'transfer' ? 'selected' : '' ?>>🏦 TRANSFER BANK</option>
                            <option value="ewallet" <?= $transaksi->metode == 'ewallet' ? 'selected' : '' ?>>📱 E-WALLET (QRIS)</option>
                            <option value="debit" <?= $transaksi->metode == 'debit' ? 'selected' : '' ?>>💳 DEBIT / EDC</option>
                            <option value="piutang" <?= ($transaksi->status_lunas == '0' || $transaksi->metode == 'piutang') ? 'selected' : '' ?>>⏳ PIUTANG / TEMPO</option>
                        </select>
                    </div>

                    <div class="mt-3 payment-method-section" id="transfer_section" style="<?= $transaksi->metode == 'transfer' ? '' : 'display: none;' ?>">
                        <select name="rekening_id" class="form-select select2">
                            <option value="">Pilih Rekening Tujuan...</option>
                            <?php if (isset($rekening)):
                                foreach ($rekening as $r): ?>
                                    <option value="<?= $r->id ?>" <?= $transaksi->bank_id == $r->id ? 'selected' : '' ?>><?= $r->nama_bank ?> - <?= $r->no_rekening ?></option>
                                <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="mt-3 payment-method-section" id="ewallet_section" style="<?= $transaksi->metode == 'ewallet' ? '' : 'display: none;' ?>">
                        <select name="ewallet_id" class="form-select select2">
                            <option value="">Pilih Akun E-Wallet...</option>
                            <?php if (isset($ewallet)):
                                foreach ($ewallet as $e): ?>
                                    <option value="<?= $e->id ?>" <?= $transaksi->ewallet_id == $e->id ? 'selected' : '' ?>><?= $e->ewallet_nama ?></option>
                                <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="mt-3 payment-method-section" id="edc_section" style="<?= $transaksi->metode == 'debit' ? '' : 'display: none;' ?>">
                        <select name="edc_id" class="form-select select2">
                            <option value="">Pilih Mesin EDC...</option>
                            <?php if (isset($edc)):
                                foreach ($edc as $ed): ?>
                                    <option value="<?= $ed->edc_id ?>" <?= $transaksi->edc_id == $ed->edc_id ? 'selected' : '' ?>><?= $ed->edc_nama ?></option>
                                <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="mt-4">
                        <textarea name="keterangan" class="form-control rounded-3 small" rows="2"
                            placeholder="Catatan internal transaksi..."><?= $transaksi->keterangan ?></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="submitBtn"
                            class="btn btn-warning w-100 rounded-pill py-3 fw-bold shadow-lg">
                            SIMPAN PERUBAHAN <i class="fas fa-save ms-2"></i>
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.select2').not('#konsumen_id').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Pilih...' });

        $('#konsumen_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Ketik Nama / No HP Konsumen...',
            allowClear: true,
            ajax: {
                url: '<?= base_url('penjualan/search_konsumen_ajax') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            },
            minimumInputLength: 1
        });

        $('#product_id').select2({
            theme: 'bootstrap-5', width: '100%', placeholder: 'Cari Produk...', tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') return null;
                return { id: term, text: term + ' (Produk Baru)', newTag: true }
            }
        });

        // Initialize Cart with existing items from PHP
        let cart = <?php 
            $cart_data = [];
            if (!empty($transaksi->products)) {
                foreach ($transaksi->products as $p) {
                    $cart_data[] = [
                        'id' => $p->product_id,
                        'name' => $p->product_nama,
                        'jumlah' => (int)$p->qty,
                        'harga' => (float)$p->unit_price,
                        'subtotal' => (float)($p->qty * $p->unit_price)
                    ];
                }
            }
            echo json_encode($cart_data);
        ?>;
        
        updateCartDisplay();

        function formatIDR(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(val);
        }

        $('#product_id').on('select2:select', function (e) {
            const data = e.params.data;
            let harga = 0;
            if (!data.newTag) {
                const selected = $(this).find('option:selected');
                harga = selected.data('harga') || 0;
            }
            $('#harga_satuan').val(harga);
            $('#jumlah').focus();
        });

        $('#addProductBtn').click(function () {
            const productId = $('#product_id').val();
            const selectedOption = $('#product_id option:selected');
            let productName = selectedOption.text();
            if (productName.includes('(Produk Baru)')) productName = productId;

            const jumlah = parseInt($('#jumlah').val()) || 0;
            const harga = parseFloat($('#harga_satuan').val()) || 0;

            if (!productId || jumlah <= 0) {
                Swal.fire({ title: 'Data Tidak Lengkap', text: 'Pastikan produk dan jumlah sudah diisi.', icon: 'warning' });
                return;
            }

            const subtotal = jumlah * harga;
            const existingIdx = cart.findIndex(i => i.id == productId);
            if (existingIdx > -1) {
                cart[existingIdx].jumlah += jumlah;
                cart[existingIdx].subtotal += subtotal;
            } else {
                cart.push({ id: productId, name: productName, jumlah: jumlah, harga: harga, subtotal: subtotal });
            }

            updateCartDisplay();
            $('#product_id').val('').trigger('change');
            $('#jumlah').val(1);
            $('#harga_satuan').val('');
        });

        window.removeFromCart = function (index) {
            cart.splice(index, 1);
            updateCartDisplay();
        };

        function updateCartDisplay() {
            const tbody = $('#cartBody');
            tbody.empty();

            if (cart.length === 0) {
                $('#cartSection').hide();
                $('#emptyCart').show();
                $('#submitBtn').prop('disabled', true);
                updateSummary(0);
                return;
            }

            $('#emptyCart').hide();
            $('#cartSection').show();

            let total = 0;
            let totalItems = 0;

            cart.forEach((item, index) => {
                total += item.subtotal;
                totalItems += item.jumlah;
                tbody.append(`
                    <tr>
                        <td class="ps-0">
                            <div class="fw-bold text-dark">${item.name}</div>
                            <small class="text-muted">Item ID: ${item.id}</small>
                        </td>
                        <td class="text-center fw-800">${item.jumlah}</td>
                        <td class="text-end mono">${formatIDR(item.harga)}</td>
                        <td class="text-end mono fw-800 text-dark">${formatIDR(item.subtotal)}</td>
                        <td class="text-center pe-0">
                            <button type="button" class="btn-action-circle" onclick="removeFromCart(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            updateSummary(total, totalItems);
            $('#submitBtn').prop('disabled', false);
        }

        function updateSummary(total, items = 0) {
            $('#total_subtotal').text(formatIDR(total));
            $('#total_tagihan').text(formatIDR(total));
            $('#total_items').text(items);
        }

        $('#metode_bayar').change(function () {
            $('.payment-method-section').slideUp(200);
            const method = $(this).val();
            if (method === 'transfer') $('#transfer_section').slideDown(300);
            else if (method === 'ewallet') $('#ewallet_section').slideDown(300);
            else if (method === 'debit') $('#edc_section').slideDown(300);
        });

        $('#transactionForm').submit(function (e) {
            e.preventDefault();
            if (cart.length === 0) return;

            const formData = new FormData(this);
            cart.forEach((item, i) => {
                formData.append(`products[${i}][product_id]`, item.id);
                formData.append(`products[${i}][product_name]`, item.name);
                formData.append(`products[${i}][jumlah]`, item.jumlah);
                formData.append(`products[${i}][harga_satuan]`, item.harga);
            });

            const btn = $('#submitBtn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> SAVING...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false, contentType: false, dataType: 'json',
                success: function (resp) {
                    if (resp.status === 'success') {
                        Swal.fire({
                            title: 'Updated!',
                            text: resp.message,
                            icon: 'success',
                            confirmButtonColor: '#4338ca'
                        }).then(() => {
                            window.location.href = '<?= base_url('penjualan/manual_list') ?>';
                        });
                    } else {
                        Swal.fire('Error', resp.message, 'error');
                        btn.prop('disabled', false).html('SIMPAN PERUBAHAN <i class="fas fa-save ms-2"></i>');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Connection failed.', 'error');
                    btn.prop('disabled', false).html('SIMPAN PERUBAHAN <i class="fas fa-save ms-2"></i>');
                }
            });
        });
    });
</script>
