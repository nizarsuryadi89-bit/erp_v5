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

    .input-group-premium {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }

    .input-group-premium:focus-within {
        background: white;
        border-color: var(--tgm-primary);
        box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.1);
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

    /* Select2 Premium Overrides */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        height: 45px !important;
        display: flex !important;
        align-items: center !important;
    }

    .recent-trx-pill {
        background: white;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        border: 1px solid #f1f5f9;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .recent-trx-pill:hover {
        border-color: var(--tgm-primary);
        transform: translateX(5px);
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
                            <h4 class="fw-800 text-dark mb-0">ORDER CHECKOUT</h4>
                            <small class="text-muted">Pencatatan Transaksi Barang Jadi & Jasa Manual</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('penjualan/manual_list') ?>"
                                class="btn btn-light rounded-pill px-4 fw-bold border">
                                <i class="fas fa-list me-2"></i> RIWAYAT NOTA
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form id="transactionForm" action="<?= base_url('penjualan/simpan') ?>" method="post">
                            <div class="row g-3 mb-4">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">IDENTITAS KONSUMEN</label>
                                    <select id="konsumen_id" name="konsumen_id" class="form-select select2" required>
                                        <option value="">Pilih / Cari Konsumen...</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">TANGGAL TRANSAKSI</label>
                                    <input type="date" id="tanggal_transaksi" name="tanggal_transaksi"
                                        class="form-control rounded-pill px-3" required value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">SALES / PIC</label>
                                    <input type="text" name="sales_person" class="form-control rounded-pill px-3"
                                        placeholder="Nama Sales..." value="<?= $this->session->userdata('username') ?>">
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

                            <div class="table-responsive" id="cartSection" style="display: none;">
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

                            <div id="emptyCart" class="text-center py-5">
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
                            <option value="cash">💵 TUNAI / CASH</option>
                            <option value="transfer">🏦 TRANSFER BANK</option>
                            <option value="ewallet">📱 E-WALLET (QRIS)</option>
                            <option value="debit">💳 DEBIT / EDC</option>
                            <option value="piutang">⏳ PIUTANG / TEMPO</option>
                        </select>
                    </div>

                    <!-- Dynamic Payment Contexts -->
                    <div class="mt-3 payment-method-section" id="transfer_section" style="display: none;">
                        <select name="rekening_id" class="form-select select2">
                            <option value="">Pilih Rekening Tujuan...</option>
                            <?php if (isset($rekening)):
                                foreach ($rekening as $r): ?>
                                    <option value="<?= $r->id ?>"><?= $r->nama_bank ?> - <?= $r->no_rekening ?></option>
                                <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="mt-3 payment-method-section" id="ewallet_section" style="display: none;">
                        <select name="ewallet_id" class="form-select select2">
                            <option value="">Pilih Akun E-Wallet...</option>
                            <?php if (isset($ewallet)):
                                foreach ($ewallet as $e): ?>
                                    <option value="<?= $e->id ?>"><?= $e->ewallet_nama ?></option>
                                <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="mt-3 payment-method-section" id="edc_section" style="display: none;">
                        <select name="edc_id" class="form-select select2">
                            <option value="">Pilih Mesin EDC...</option>
                            <?php if (isset($edc)):
                                foreach ($edc as $ed): ?>
                                    <option value="<?= $ed->edc_id ?>"><?= $ed->edc_nama ?></option>
                                <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="mt-4">
                        <textarea name="keterangan" class="form-control rounded-3 small" rows="2"
                            placeholder="Catatan internal transaksi..."></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="submitBtn"
                            class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" disabled>
                            COMPLETTE TRANSACTION <i class="fas fa-check-circle ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-link text-muted w-100 mt-2 small text-decoration-none"
                            onclick="location.reload()">
                            Reset Order
                        </button>
                    </div>
                    </form>
                </div>

                <!-- Mini History -->
                <div class="mt-4 p-2">
                    <h6 class="fw-800 text-dark mb-3"><i class="fas fa-history me-2"></i>NOTS TERAKHIR</h6>
                    <?php if (!empty($transaksi_terakhir)):
                        foreach (array_slice($transaksi_terakhir, 0, 3) as $t): ?>
                            <div class="recent-trx-pill d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark small"><?= $t->nospk ?></div>
                                    <div class="text-muted" style="font-size: 0.65rem;"><?= $t->konsumen_nama ?></div>
                                </div>
                                <div class="mono fw-bold text-success" style="font-size: 0.8rem;">Rp
                                    <?= number_format($t->total_tagihan ?? 0) ?>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                        <div class="text-muted small italic">Tidak ada history hari ini</div>
                    <?php endif; ?>
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
        // Initialize Select2 with Premium Style & AJAX Search
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
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        $('#product_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Cari Produk atau Ketik Manual...',
            tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term,
                    text: term + ' (Produk Baru)',
                    newTag: true
                }
            }
        });

        let cart = [];

        // Format Currency Helper
        const formatIDR = (val) => {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(val);
        };

        // Product Selection Logic
        $('#product_id').on('select2:select', function (e) {
            const data = e.params.data;
            let harga = 0;

            // If it's an existing product from database (has data-harga)
            if (!data.newTag) {
                const selected = $(this).find('option:selected');
                harga = selected.data('harga') || 0;
            }

            $('#harga_satuan').val(harga);
            $('#jumlah').focus();
        });

        // Add to Cart with Animation
        $('#addProductBtn').click(function () {
            const productId = $('#product_id').val();
            const selectedOption = $('#product_id option:selected');

            // If it's a new tag, use the text, otherwise use the option text
            let productName = selectedOption.text();
            if (productName.includes('(Produk Baru)')) {
                productName = productId; // Use the raw input as name
            }

            const jumlah = parseInt($('#jumlah').val()) || 0;
            const harga = parseFloat($('#harga_satuan').val()) || 0;

            if (!productId || jumlah <= 0) {
                Swal.fire({
                    title: 'Data Tidak Lengkap',
                    text: 'Pastikan produk dan jumlah sudah diisi dengan benar.',
                    icon: 'warning',
                    confirmButtonColor: '#4338ca',
                    customClass: { confirmButton: 'rounded-pill px-4' }
                });
                return;
            }

            const subtotal = jumlah * harga;

            // Add or Update Cart
            const existingIdx = cart.findIndex(i => i.id === productId);
            if (existingIdx > -1) {
                cart[existingIdx].jumlah += jumlah;
                cart[existingIdx].subtotal += subtotal;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    jumlah: jumlah,
                    harga: harga,
                    subtotal: subtotal
                });
            }

            updateCartDisplay();

            // Reset & Focus
            $('#product_id').val('').trigger('change');
            $('#jumlah').val(1);
            $('#harga_satuan').val('');
        });

        // Remove from Cart
        window.removeFromCart = function (index) {
            cart.splice(index, 1);
            updateCartDisplay();
        };

        function updateCartDisplay() {
            const tbody = $('#cartBody');
            tbody.empty();

            if (cart.length === 0) {
                $('#cartSection').fadeOut(200);
                $('#emptyCart').fadeIn(400);
                $('#submitBtn').prop('disabled', true).removeClass('shadow-lg');
                updateSummary(0);
                return;
            }

            $('#emptyCart').hide();
            $('#cartSection').fadeIn(300);

            let total = 0;
            let totalItems = 0;

            cart.forEach((item, index) => {
                total += item.subtotal;
                totalItems += item.jumlah;
                tbody.append(`
                    <tr class="animate__animated animate__fadeIn">
                        <td class="ps-0">
                            <div class="fw-bold text-dark">${item.name}</div>
                            <small class="text-muted">Item ID: ${item.id}</small>
                        </td>
                        <td class="text-center fw-800">${item.jumlah}</td>
                        <td class="text-end mono">${formatIDR(item.harga)}</td>
                        <td class="text-end mono fw-800 text-dark">${formatIDR(item.subtotal)}</td>
                        <td class="text-center pe-0">
                            <button type="button" class="btn-action-circle" onclick="removeFromCart(${index})" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            updateSummary(total, totalItems);
            $('#submitBtn').prop('disabled', false).addClass('shadow-lg');
        }

        function updateSummary(total, items = 0) {
            $('#total_subtotal').text(formatIDR(total));
            $('#total_tagihan').text(formatIDR(total)).addClass('animate__animated animate__pulse');
            $('#total_items').text(items);

            // Remove animation class after it finishes
            setTimeout(() => $('#total_tagihan').removeClass('animate__animated animate__pulse'), 1000);
        }

        // Payment Method Context Toggling
        $('#metode_bayar').change(function () {
            $('.payment-method-section').slideUp(200);
            const method = $(this).val();
            if (method === 'transfer') $('#transfer_section').slideDown(300);
            else if (method === 'ewallet') $('#ewallet_section').slideDown(300);
            else if (method === 'debit') $('#edc_section').slideDown(300);
        });

        // AJAX Submission
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
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> PROCESSING...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (resp) {
                    if (resp.status === 'success' || resp.success) {
                        Swal.fire({
                            title: 'Order Completed!',
                            text: resp.message || 'Transaksi berhasil disimpan ke sistem.',
                            icon: 'success',
                            confirmButtonColor: '#4338ca',
                            confirmButtonText: 'Cetak Nota & Lanjutkan',
                            customClass: { confirmButton: 'rounded-pill px-5 py-3 fw-bold' }
                        }).then(() => {
                            if (resp.print_url) window.open(resp.print_url, '_blank');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', resp.message || 'Terjadi gangguan sistem.', 'error');
                        btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function () {
                    Swal.fire('Connection Error', 'Gagal menghubungi server. Periksa koneksi internet Anda.', 'error');
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>