<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    const SPKState = {
        nospk: '<?= $nospk ?>',
        isStep2: <?= $this->uri->segment(3) != NULL ? 'true' : 'false' ?>,
        itemCount: 0,
        totalHarga: 0,
        isProcessing: false,
        formatIDR: (val) => new Intl.NumberFormat('id-ID').format(val),
        handleAjaxError: (xhr, status, error) => {
            const errorMsg = xhr ? xhr.responseText : (error || 'Unknown Error');
            console.error('AJAX Error:', status, error, errorMsg);
            SPKState.setProcessing(false);
            Toast.fire({ icon: 'error', title: 'Gangguan Koneksi', text: 'Gagal menghubungi server.' });
        },
        setProcessing: (processing) => {
            SPKState.isProcessing = processing;
            if (processing) {
                $('.section-body, #transaksi').addClass('processing-blur');
                if ($('#product-loading-overlay').length) $('#product-loading-overlay').css('display', 'flex');
            } else {
                $('.section-body, #transaksi').removeClass('processing-blur');
                if ($('#product-loading-overlay').length) $('#product-loading-overlay').hide();
            }
        }
    };

    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
    });

    $(document).ready(function () {
        initializeSelect2();
        initializeEventHandlers();
        
        // Autofocus Logic
        if (SPKState.isStep2) {
            // Step 2: Langsung fokus ke Scan Produk
            setTimeout(() => $('#productSelect').select2('open'), 500);
            syncTransactionData();
            // Bind live price estimation for already-loaded product
            setTimeout(() => bindPriceEstimation(), 600);
        } else {
            // Step 1: Langsung fokus ke Pilih Konsumen
            setTimeout(() => $('#konsumenSelect1').select2('open'), 500);
        }
    });

    async function syncTransactionData() {
        if (!SPKState.nospk) return;
        try {
            const [tableHtml, summaryData] = await Promise.all([
                $.ajax({ url: '<?= base_url('transaksi/load_transaksi/') ?>' + SPKState.nospk, type: 'GET' }),
                $.ajax({ url: '<?= base_url('transaksi/loadsummary/') ?>' + SPKState.nospk, type: 'GET', dataType: 'json' })
            ]);
            $('#transaksi tbody').html(tableHtml);
            SPKState.itemCount = summaryData.jml_item;
            SPKState.totalHarga = summaryData.total_harga;
            $('#jmlItem').text(SPKState.itemCount);
            $('#totalHarga').text(SPKState.formatIDR(SPKState.totalHarga));
            $('#total_pemesanan').val(SPKState.totalHarga);

            // Populate Linked Item Dropdown based on table contents
            populateLinkedItemDropdown();

            // Reset Checkbox state
            $('#checkAllItem').prop('checked', false);
            updateBulkActionUI();

            updateButtonState();
        } catch (err) { SPKState.handleAjaxError(null, 'error', err); }
    }

    function populateLinkedItemDropdown() {
        const select = $('#linked_item_select');
        select.empty();
        select.append('<option value="">-- Tidak Dikaitkan --</option>');
        
        let hasItems = false;
        $('#transaksi tbody tr').each(function(index) {
            // Find product name in paragraph inside td
            const pName = $(this).find('td:eq(2) p.fw-bold').text().trim();
            if (pName && pName !== 'Belum Ada Item Dalam SPK Ini' && pName !== '') {
                // Extract variations from small tags
                let vars = [];
                $(this).find('td:eq(2) small').each(function() {
                    let text = $(this).text().trim();
                    if (text !== '') vars.push(text);
                });
                
                let fullName = pName;
                if (vars.length > 0) {
                    fullName += ' (' + vars.join(' | ') + ')';
                }
                
                // Final descriptive string (removed Qty of target item to prevent operator confusion)
                const descStr = `[Item #${index + 1}] ${fullName}`;
                
                select.append(`<option value="${descStr}">${descStr}</option>`);
                hasItems = true;
            }
        });

        if (hasItems) {
            $('#linked-item-container').slideDown(200);
        } else {
            $('#linked-item-container').slideUp(200);
        }
    }

    function updateBulkActionUI() {
        const selectedCount = $('.item-checkbox:checked').length;
        if (selectedCount > 0) {
            $('#bulkActionContainer').slideDown(200);
            $('#countSelected').text(selectedCount);
        } else {
            $('#bulkActionContainer').slideUp(200);
        }
    }

    function initializeSelect2() {
        $('#konsumenSelect1').select2({
            theme: 'bootstrap-5', placeholder: 'Ketik Nama / WA / Email...', minimumInputLength: 1, width: '100%',
            ajax: { url: '<?= site_url("Transaksi/search_konsumen") ?>', dataType: 'json', delay: 250, data: (params) => ({ q: params.term || '' }), processResults: (data) => ({ results: $.map(data, (item) => ({ id: item.konsumen_id || item.id, text: (item.konsumen_nama || item.nama) + ' (' + item.konsumen_nohp + ')' + (item.konsumen_email ? ' - ' + item.konsumen_email : '') })) }), cache: true }
        }).on('select2:select', () => $('#submitBtn').prop('disabled', false).addClass('btn-premium-primary animate__animated animate__pulse'));

        $('#productSelect').select2({
            theme: 'bootstrap-5', placeholder: 'Scan Barcode / Nama Produk...', allowClear: true, width: '100%',
            ajax: {
                url: '<?= site_url("Transaksi/search_product") ?>',
                dataType: 'json',
                delay: 300,
                data: (p) => ({ q: p.term || '', nospk: SPKState.nospk }),
                processResults: (d) => ({ results: d }),
                cache: true
            }
        }).on('select2:select', (e) => loadProductDetails(e.params.data.id));
    }

    function loadProductDetails(productId) {
        SPKState.setProcessing(true);
        $.ajax({
            url: '<?= base_url('transaksi/get_product_details_ajax') ?>', type: 'GET', data: { product_id: productId }, dataType: 'json',
            success: (res) => {
                SPKState.setProcessing(false);
                if (res.status === 'success') {
                    const p = res.product;
                    $('#hidden_product_id').val(p.product_id);
                    $('#hidden_kategori_id').val(p.kategori_id);
                    $('#hidden_subkategori_id').val(p.subkategori_id);

                    // Sync visible selects
                    $('#kategori_id').val(p.kategori_id);
                    $('#subkategori_id').val(p.subkategori_id);

                    $('#qty').val(1);
                    $('#selected-product-name').text(p.product_nama);
                    $('#dynamic-category-view').html(res.cat_view);
                    setTimeout(() => { $('#qty').focus().select(); }, 100);

                    // Bind live estimation after dynamic view loads
                    setTimeout(() => bindPriceEstimation(), 300);
                }
            },
            error: SPKState.handleAjaxError
        });
    }

    let _estTimer = null;
    function estimatePrice() {
        clearTimeout(_estTimer);
        _estTimer = setTimeout(() => {
            const productId = $('#hidden_product_id').val();
            const qty = parseFloat($('#qty').val());
            if (!productId || !qty || qty <= 0) {
                $('#price-estimation-box').addClass('d-none');
                $('#price-estimation-warning').addClass('d-none');
                return;
            }

            // Collect all form data from form-add-temp
            const formData = $('#form-add-temp').serialize();

            $.ajax({
                url: '<?= base_url('transaksi/estimate_price') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#est-harga-satuan').text('Rp ' + SPKState.formatIDR(res.harga_satuan));
                        $('#est-subtotal').text('Rp ' + SPKState.formatIDR(res.subtotal));
                        
                        if (res.is_min_applied) {
                            $('#est-min-warning').removeClass('d-none');
                        } else {
                            $('#est-min-warning').addClass('d-none');
                        }
                        
                        $('#price-estimation-box').removeClass('d-none');
                        $('#price-estimation-warning').addClass('d-none');
                    } else if (res.status === 'not_found') {
                        $('#price-estimation-box').addClass('d-none');
                        $('#est-warning-text').text(res.message || 'Harga belum dikonfigurasi');
                        $('#price-estimation-warning').removeClass('d-none');
                    } else {
                        $('#price-estimation-box').addClass('d-none');
                        $('#price-estimation-warning').addClass('d-none');
                    }
                },
                error: function() {
                    $('#price-estimation-box').addClass('d-none');
                    $('#price-estimation-warning').addClass('d-none');
                }
            });
        }, 400);
    }

    function bindPriceEstimation() {
        // Bind to qty and all dynamic form fields
        $('#qty').off('input.est').on('input.est', estimatePrice);
        $('#form-add-temp').find('select, input[type="radio"]').off('change.est').on('change.est', estimatePrice);
        $('#form-add-temp').find('input[name="panjang"], input[name="lebar"]').off('input.est').on('input.est', estimatePrice);
        // Trigger initial estimate if qty has value
        if (parseFloat($('#qty').val()) > 0) estimatePrice();
    }

    function initializeEventHandlers() {
        // ... (existing submit handlers)

        // Checkbox Logic: Select All
        $(document).on('change', '#checkAllItem', function () {
            $('.item-checkbox').prop('checked', $(this).is(':checked'));
            updateBulkActionUI();
        });

        // Checkbox Logic: Individual
        $(document).on('change', '.item-checkbox', function () {
            const allChecked = $('.item-checkbox').length === $('.item-checkbox:checked').length;
            $('#checkAllItem').prop('checked', allChecked);
            updateBulkActionUI();
        });

        // Sync Shipping Header to Hidden Inputs (Step 1)
        $(document).on('change', '[name="id_pengiriman"], [name="batch_pengiriman"]', function() {
            const val = $(this).val();
            const name = $(this).attr('name');
            $(`input[type="hidden"][name="${name}"]`).val(val);
        });

        // Bulk Delete Action
        $(document).on('click', '#btnHapusMassal', function () {
            const ids = [];
            $('.item-checkbox:checked').each(function () { ids.push($(this).val()); });

            if (ids.length === 0) return;
            
            // Check for linked items in the cart
            let hasLinkedItems = false;
            $('#transaksi tbody tr').each(function() {
                const ket = $(this).find('td:eq(3)').text() || '';
                if (ket.includes('[TERAPKAN PADA:')) {
                    hasLinkedItems = true;
                }
            });
            
            let warningText = "Item yang dipilih akan dihapus permanen dari daftar.";
            if (hasLinkedItems) {
                warningText = "AWAS! Ada Jasa/Produk yang saling dikaitkan di keranjang ini. Harap periksa ulang catatan Jasa Anda jika barang utamanya dihapus.";
            }

            Swal.fire({
                title: `Hapus ${ids.length} Item?`,
                text: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    SPKState.setProcessing(true);
                    $.ajax({
                        url: '<?= base_url("transaksi/delete_temp_item") ?>',
                        type: 'POST',
                        data: { temp_id: ids }, // Sending array
                        success: (res) => {
                            SPKState.setProcessing(false);
                            Toast.fire({ icon: 'success', title: `${ids.length} Item Berhasil Dihapus` });
                            syncTransactionData();
                        },
                        error: SPKState.handleAjaxError
                    });
                }
            });
        });

        $('#form-step-1').on('submit', function (e) {
            e.preventDefault();
            
            // Validation for Marketplace
            const method = $('#selected_jenis_transaksi').val();
            if (method == 2) {
                // MANUAL CUSTOMER VALIDATION
                const nama = $('#m_nama_konsumen').val();
                const alamat = $('#m_alamat_konsumen').val();
                const marketplace = $('#id_marketplace').val();
                const resi = $('input[name="resi_marketplace"]').val();

                if (nama.trim() === '' || alamat.trim() === '') {
                    Toast.fire({ icon: 'error', title: 'Nama & Alamat Pembeli wajib diisi!' });
                    $('#m_nama_konsumen').focus();
                    return false;
                }
                
                if (resi == '-' || resi == '' || resi.length < 3) {
                    Toast.fire({ icon: 'warning', title: 'No. Resi / Invoice wajib diisi yang benar' });
                    $('input[name="resi_marketplace"]').focus().select();
                    return false;
                }

                const courier = $('select[name="id_pengiriman"]').val();
                if (courier == '0' || !courier) {
                    Toast.fire({ icon: 'warning', title: 'Pilih Kurir / Expedisi terlebih dahulu' });
                    $('select[name="id_pengiriman"]').focus();
                    return false;
                }

                const batch = $('select[name="batch_pengiriman"]').val();
                if (!batch) {
                    Toast.fire({ icon: 'warning', title: 'Pilih Batch / Deadline Produksi' });
                    $('select[name="batch_pengiriman"]').focus();
                    return false;
                }
            } else {
                // STANDARD CONSUMER VALIDATION
                const konsumenId = $('#konsumenSelect1').val();
                if (!konsumenId) {
                    Toast.fire({ icon: 'warning', title: 'Pilih Konsumen terlebih dahulu' });
                    $('#konsumenSelect1').select2('open');
                    return false;
                }
            }

            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('Memproses...');
            $.ajax({ url: '<?= base_url('transaksi/step_satu') ?>', method: 'POST', data: $(this).serialize(), dataType: 'json', success: (res) => { if (res.status === 'success') { window.location.href = '<?= base_url('transaksi/createspk/') ?>' + res.nospk; } else { btn.prop('disabled', false).html('MULAI'); Toast.fire({ icon: 'error', title: res.message }); } }, error: SPKState.handleAjaxError });
        });

        $('#formTambahKonsumen').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

            $.ajax({
                url: '<?= base_url('konsumen/actionAddAjax') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: (res) => {
                    btn.prop('disabled', false).html(originalText);
                    if (res.status === 'success') {
                        Toast.fire({ icon: 'success', title: 'Konsumen Berhasil Ditambahkan' });
                        $('#modalTambahKonsumen').modal('hide');
                        $('#formTambahKonsumen')[0].reset();
                        
                        // Automatically select the newly created customer
                        if (res.data) {
                            var newOption = new Option(res.data.konsumen_nama + ' (' + res.data.konsumen_nohp + ')', res.data.konsumen_id, true, true);
                            $('#konsumenSelect1').append(newOption).trigger('change');
                        }
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal menyimpan data', 'error');
                    }
                },
                error: (xhr, status, error) => {
                    btn.prop('disabled', false).html(originalText);
                    SPKState.handleAjaxError(xhr, status, error);
                }
            });
        });

        $('#form-add-temp').on('submit', function (e) {
            e.preventDefault(); if (SPKState.isProcessing) return;
            
            // NEW FEATURE: LINKED ITEM (Inject into keterangan)
            const linkedItem = $('#linked_item_select').val();
            let originalKeterangan = $('textarea[name="keterangan"]').val();
            if (linkedItem) {
                const ketField = $('textarea[name="keterangan"]');
                let currentKet = ketField.val().trim();
                let appendText = `[TERAPKAN PADA: ${linkedItem}]`;
                
                if (currentKet !== '') {
                    ketField.val(currentKet + '\n' + appendText);
                } else {
                    ketField.val(appendText);
                }
            }

            SPKState.setProcessing(true);
            $.ajax({
                url: '<?= base_url('transaksi/action_add_temp') ?>', method: 'POST', data: $(this).serialize(), dataType: 'json',
                success: (res) => {
                    // Revert textarea to original in case of error or just to be clean
                    $('textarea[name="keterangan"]').val(originalKeterangan);
                    
                    SPKState.setProcessing(false);
                    if (res.status === 'success') {
                        Toast.fire({ icon: 'success', title: 'Item ditambahkan' });
                        syncTransactionData();
                        $('#productSelect').val(null).trigger('change').select2('open');
                        $('#qty').val('');
                        $('textarea[name="keterangan"]').val(''); // Reset keterangan on success
                        $('#linked_item_select').val(''); // Reset dropdown
                    } else { Swal.fire('Gagal', res.message, 'error'); }
                },
                error: SPKState.handleAjaxError
            });
        });

        $(document).on('click', '.btn_del', function () {
            const id = $(this).attr('id');
            
            // Check for linked items in the cart
            let hasLinkedItems = false;
            $('#transaksi tbody tr').each(function() {
                const ket = $(this).find('td:eq(3)').text() || '';
                if (ket.includes('[TERAPKAN PADA:')) {
                    hasLinkedItems = true;
                }
            });
            
            let warningText = "Anda tidak akan dapat mengembalikan ini!";
            if (hasLinkedItems) {
                warningText = "AWAS! Ada Jasa/Produk yang saling dikaitkan di keranjang ini. Harap periksa ulang catatan Jasa Anda jika barang utamanya dihapus.";
            }

            Swal.fire({ title: 'Hapus Item?', text: warningText, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus' }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({ url: '<?= base_url("transaksi/delete_temp_item") ?>', type: 'POST', data: { temp_id: id }, success: () => { Toast.fire({ icon: 'success', title: 'Terhapus' }); syncTransactionData(); } });
                }
            });
        });

        $('#btnKirimKasir').click(function () {
            // Gunakan ID Konsumen yang sudah ada di database untuk SPK ini
            const currentKonsumenId = '<?= $getkosumen["konsumen_id"] ?? "" ?>';
            const idJenisTransaksi = '<?= $getkosumen["id_jenis_transaksi"] ?? "1" ?>';

            if (!currentKonsumenId || currentKonsumenId == '0') {
                Swal.fire({
                    title: 'Konsumen Kosong!',
                    text: 'Data konsumen belum terdeteksi. Silakan coba pilih ulang konsumen.',
                    icon: 'warning',
                    confirmButtonText: 'OKE',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            let confirmTitle = 'Kirim Invoice?';
            let confirmText = 'Pastikan semua item sudah benar. Data akan dikirim ke Kasir.';
            let confirmBtn = 'Ya, Kirim Sekarang';

            if (idJenisTransaksi === '2') {
                confirmTitle = 'Kirim ke Produksi?';
                confirmText = 'Pesanan Marketplace dideteksi. Pesanan akan langsung masuk antrian produksi (Bypass Kasir).';
                confirmBtn = 'Ya, Prosess Produksi';
            }

            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: confirmBtn,
                cancelButtonText: 'Cek Lagi',
                confirmButtonColor: idJenisTransaksi === '2' ? '#10b981' : '#3b82f6'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('form').submit();
                }
            });
        });
    }


    function updateButtonState() {
        const btn = $('#btnKirimKasir');
        const idJenisTransaksi = '<?= $getkosumen["id_jenis_transaksi"] ?? "1" ?>';

        if (SPKState.itemCount > 0) {
            if (idJenisTransaksi === '2') {
                btn.prop('disabled', false).removeClass('btn-secondary btn-premium-warning').addClass('btn-premium-success').html('<i class="fas fa-check-double me-2"></i>KIRIM KE PRODUKSI');
            } else {
                btn.prop('disabled', false).removeClass('btn-secondary btn-premium-success').addClass('btn-premium-warning').html('<i class="fas fa-cash-register me-2"></i>KIRIM KE KASIR');
            }
        } else {
            btn.prop('disabled', true).removeClass('btn-premium-warning btn-premium-success').addClass('btn-secondary').html('ITEM KOSONG');
        }
    }

    function selectOrderMethod(val, el) {
        $('.choice-card').removeClass('active'); 
        $(el).addClass('active');
        $('#selected_jenis_transaksi').val(val);
        
        const $step2Title = $('#step2_title');
        const $badgeCont = $('#cust_badge_type');

        if (val == 2) { 
            // MARKETPLACE MODE
            $('#konsumen_db_search').hide();
            $('#konsumen_manual_input').show();
            $step2Title.text('DATA PEMBELI MARKETPLACE');
            $badgeCont.html('<span class="badge bg-warning text-dark fw-bold border" style="font-size: 0.65rem;">ONE-TIME CUSTOMER</span>');
            
            // Focus Resi for efficiency
            setTimeout(() => {
                $('input[name="resi_marketplace"]').focus().select();
            }, 100);
        } else { 
            // STANDARD / WA MODE
            $('#konsumen_db_search').show();
            $('#konsumen_manual_input').hide();
            $step2Title.text('LENGKAPI DATA PELANGGAN');
            $badgeCont.html('<span class="badge bg-light text-dark fw-bold border" style="font-size: 0.65rem;">DATABASE CRM</span>');
            
            if (val == 3) {
                 $badgeCont.html('<span class="badge bg-info text-white fw-bold border" style="font-size: 0.65rem;">CRM - WA/EMAIL</span>');
            }
        }
        
        validateStep1Button();
    }

    // Helper to validate step 1 based on flow
    function validateStep1Button() {
        const method = $('#selected_jenis_transaksi').val();
        let isValid = false;

        if (method == 2) {
            const nama = $('#m_nama_konsumen').val();
            isValid = (nama.trim().length > 2);
        } else {
            isValid = ($('#konsumenSelect1').val() !== null);
        }

        $('#submitBtn').prop('disabled', !isValid);
    }

    // Bind manual inputs for validation
    $(document).on('keyup change', '#m_nama_konsumen, #m_alamat_konsumen', function() {
        validateStep1Button();
    });

    function copyToClipboard() {
        navigator.clipboard.writeText($('#text-copy').val()).then(() => Toast.fire({ icon: 'success', title: 'SPK Tersalin' }));
    }

    function openKioskImport() { $('#modalImportKiosk').modal('show'); loadKioskOrders(); }
    function loadKioskOrders() {
        $.ajax({
            url: '<?= base_url('kiosk/list_pending_orders') ?>', type: 'GET', dataType: 'json', success: (res) => {
                let html = '';
                res.data.forEach(d => { html += `<tr><td class="ps-4">${d.group_ref}</td><td>${d.nama}</td><td class="text-center">${d.item_count}</td><td>${d.formatted_total}</td><td class="text-end pe-4"><button class="btn btn-xs btn-primary" onclick="processKioskOrder('${d.group_ref}')">PROSES</button></td></tr>`; });
                $('#kioskOrderList').html(html || '<tr><td colspan="5" class="text-center">Kosong</td></tr>');
            }
        });
    }
    function processKioskOrder(ref) { $.ajax({ url: '<?= base_url('kiosk/fetch_for_import') ?>', type: 'POST', data: { no_ref: ref }, dataType: 'json', success: (res) => { if (res.status === 'success') window.location.href = res.redirect; } }); }
</script>