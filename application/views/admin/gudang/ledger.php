<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - STOCK LEDGER (KARTU STOK)
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Warehouse Selector Tabs */
    .warehouse-nav-container {
        padding: 0.5rem;
        background: white;
        border-radius: 16px;
        box-shadow: var(--premium-shadow);
        display: inline-flex;
        gap: 8px;
        margin-bottom: 2rem;
        border: 1px solid #f1f5f9;
        max-width: 100%;
        overflow-x: auto;
    }

    .nav-btn-premium {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        color: #64748b;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        background: transparent;
        white-space: nowrap;
        text-decoration: none !important;
    }

    .nav-btn-premium:hover {
        background: #f8fafc;
        color: #1a1c23;
    }

    .nav-btn-premium.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    /* Table Styles */
    .report-section-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.25rem 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        padding: 1rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    /* Badge Types */
    .type-badge-premium {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
    }

    .qty-in {
        color: #27ae60;
        font-weight: 900;
    }

    .qty-out {
        color: #f72585;
        font-weight: 900;
    }

    .balance-box {
        background: #f8fafc;
        font-weight: 900;
        color: #1a1c23;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
    }

    /* Loader */
    .ledger-loader-container {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
    }

    .ledger-loader-container.active {
        display: flex;
    }

    /* Animation */
    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: slideUpFade 0.5s ease-out forwards;
    }
</style>

<!-- <div class="app-content mt-2">
    <div class="container-fluid"> -->
<!-- Warehouse Selector -->
<div class="warehouse-nav-container animate-up">
    <button class="nav-btn-premium tab-btn <?= $current_gudang == 0 ? 'active' : '' ?>" data-id="0">
        <i class="fas fa-globe"></i> Semua Gudang
    </button>
    <?php foreach ($warehouses as $wh): ?>
        <button class="nav-btn-premium tab-btn <?= $current_gudang == $wh->gudang_id ? 'active' : '' ?>"
            data-id="<?= $wh->gudang_id ?>">
            <i class="fas fa-warehouse"></i> <?= $wh->gudang_nama ?>
        </button>
    <?php endforeach; ?>
</div>

<!-- Dynamic Content Section -->
<div id="ledger-wrapper" style="position: relative; min-height: 500px;">
    <div class="ledger-loader-container" id="ledgerLoader">
        <div class="text-center">
            <div class="spinner-border text-dark" style="width: 3.5rem; height: 3.5rem; border-width: 6px;"></div>
            <div class="mt-3 fw-900 text-dark text-uppercase small" style="letter-spacing: 2px;">Sinkronisasi Ledger...
            </div>
        </div>
    </div>

    <div id="ledger-content">
        <?php $this->load->view('admin/gudang/ledger_content'); ?>
    </div>
</div>
</div>
</div>

<!-- PREMIUM SELECT2 & MODAL ASSETS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Modal Manual Adjustment (Saldo Awal) -->
<div class="modal fade" id="modalAdjustment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header bg-dark text-white p-4" style="border-radius: 24px 24px 0 0;">
                <h5 class="modal-title fw-800 text-uppercase"><i class="fas fa-layer-group me-2"></i> Sinkronisasi Stok
                    Manual</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formAdjustment">
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="gudang_id" value="<?= $current_gudang ?>">

                    <div class="mb-4">
                        <label class="form-label fw-800 text-muted small text-uppercase">1. Pilih Material /
                            Varian</label>
                        <select name="material_id" class="form-select select2-material" style="width: 100%;" required>
                            <option value="">Cari nama bahan atau varian...</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-800 text-muted small text-uppercase">2. Input Stok Aktual
                                (Sekarang)</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-primary fw-900"><i
                                        class="fas fa-calculator"></i></span>
                                <input type="number" step="0.001" name="qty_aktual"
                                    class="form-control border-start-0 fw-900" placeholder="0.000" required>
                                <span class="input-group-text bg-white border-start-0 text-muted fw-bold"
                                    id="label-satuan">-</span>
                            </div>
                            <div class="small text-danger mt-1 fw-bold italic">* Sistem akan menghitung delta secara
                                otomatis.</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-800 text-muted small text-uppercase">3. Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control shadow-sm border-0" rows="3"
                            placeholder="Contoh: Saldo awal ERP, koreksi opname harian..."
                            style="border-radius: 12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white" style="border-radius: 0 0 24px 24px;">
                    <button type="button" class="btn btn-light fw-800 text-uppercase rounded-pill px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                        class="btn btn-dark fw-800 text-uppercase rounded-pill px-4 shadow-sm btn-save-adjustment">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Initialize Select2
        function initSelect2() {
            $('.select2-material').select2({
                dropdownParent: $('#modalAdjustment'),
                placeholder: 'Cari Material...',
                allowClear: true,
                ajax: {
                    url: '<?= base_url('warehouse/get_material_list_json') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                }
            });
        }

        initSelect2();

        // Label Satuan Change
        $('.select2-material').on('select2:select', function (e) {
            const data = e.params.data;
            $('#label-satuan').text(data.satuan || '-');
        });

        function loadLedger(id) {
            $('#ledgerLoader').addClass('active');
            $('.tab-btn').removeClass('active');
            $(`.tab-btn[data-id="${id}"]`).addClass('active');

            $.ajax({
                url: `<?= base_url('warehouse/ledger/') ?>${id}`,
                type: 'GET',
                success: function (html) {
                    $('#ledger-content').fadeOut(150, function () {
                        $(this).html(html).fadeIn(150);
                        $('#ledgerLoader').removeClass('active');
                        // Update hidden input in modal
                        $('input[name="gudang_id"]').val(id);
                    });

                    // Update URL
                    const url = `<?= base_url('warehouse/ledger/') ?>${id}`;
                    window.history.pushState({ id: id }, 'Stock Ledger', url);
                },
                error: function () {
                    $('#ledgerLoader').removeClass('active');
                    Swal.fire('Oops!', 'Gagal memuat data ledger.', 'error');
                }
            });
        }

        $('.tab-btn').click(function () {
            loadLedger($(this).data('id'));
        });

        // Delegate refresh
        $(document).on('click', '.btn-refresh-ledger', function () {
            loadLedger($('.tab-btn.active').data('id'));
        });

        // Action Save Adjustment
        $('#formAdjustment').submit(function (e) {
            e.preventDefault();
            const btn = $('.btn-save-adjustment');
            const originalHtml = btn.html();

            Swal.fire({
                title: 'Konfirmasi Perubahan Stok',
                text: "Sistem akan menyesuaikan stok sesuai angka aktual yang Anda masukkan. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a1c23',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update Stok!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Memproses...');

                    $.ajax({
                        url: '<?= base_url('warehouse/actionAdjustmentStok') ?>',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Berhasil!', res.message, 'success');
                                $('#modalAdjustment').modal('hide');
                                $('#formAdjustment')[0].reset();
                                $('.select2-material').val(null).trigger('change');
                                loadLedger($('.tab-btn.active').data('id'));
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                        },
                        complete: function () {
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                }
            });
        });
    });
</script>